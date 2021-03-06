<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Feedback;
use Piwik\Common;
use Piwik\Config;
use Piwik\Container\StaticContainer;
use Piwik\IP;
use Piwik\Mail;
use Piwik\Piwik;
use Piwik\Url;
use Piwik\Version;

/**
 * API for plugin Feedback
 *
 * @method static \Piwik\Plugins\Feedback\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * Sends feedback for a specific feature to the Psstats team or alternatively to the email address configured in the
     * config: "feedback_email_address".
     *
     * @param string      $featureName  The name of a feature you want to give feedback to.
     * @param bool|int    $like         Whether you like the feature or not
     * @param string|bool $message      A message containing the actual feedback
     */
    public function sendFeedbackForFeature($featureName, $like, $message = false)
    {
        Piwik::checkUserIsNotAnonymous();
        Piwik::checkUserHasSomeViewAccess();

        $featureName = $this->getEnglishTranslationForFeatureName($featureName);

        $likeText = 'Yes';
        if (empty($like)) {
            $likeText = 'No';
        }

        $body = sprintf("Feature: %s\nLike: %s\n", $featureName, $likeText);

        $feedbackMessage = "";
        if (!empty($message) && $message != 'undefined') {
            $feedbackMessage = sprintf("Feedback:\n%s\n", trim($message));
        }
        $body .= $feedbackMessage ? $feedbackMessage : " \n";

        $subject = sprintf("%s for %s %s",
            empty($like) ? "-1" : "+1",
            $featureName,
            empty($feedbackMessage) ? "" : "(w/ feedback)"
        );

        $this->sendMail($subject, $body);
    }

    private function sendMail($subject, $body)
    {
        $feedbackEmailAddress = Config::getInstance()->General['feedback_email_address'];

        $subject = '[ Feedback Feature - Psstats ] ' . $subject;
        $body    = Common::unsanitizeInputValue($body) . "\n"
                 . 'Psstats ' . Version::VERSION . "\n"
                 . 'URL: ' . Url::getReferrer() . "\n";

        $mail = new Mail();
        $mail->setFrom(Piwik::getCurrentUserEmail());
        $mail->addTo($feedbackEmailAddress, 'Psstats Team');
        $mail->setSubject($subject);
        $mail->setBodyText($body);
        @$mail->send();
    }

    private function getEnglishTranslationForFeatureName($featureName)
    {
        $translator = StaticContainer::get('Piwik\Translation\Translator');

        if ($translator->getCurrentLanguage() == 'en') {
            return $featureName;
        }

        $translationKeyForFeature = $translator->findTranslationKeyForTranslation($featureName);

        return Piwik::translate($translationKeyForFeature, array(), 'en');
    }
}
