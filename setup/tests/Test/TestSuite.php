<?php
require_once 'Test.php';

/**
 * Class Test
 */
class TestSuite {

  /**
   * @var Test[]
   */
  protected $tests = [];

  /**
   * @param Test $test
   * @return $this
   */
  public function addTest( Test $test ) {
    $this->tests[] = $test;
    return $this;
  }

  /**
   * @return $this
   */
  public function run() {
    foreach ( $this->tests as $test ) {

      echo "\n" . get_class( $test ) . "\n";
      $test->run();

    }
    return $this;
  }

  /**
   * @return $this
   */
  public function printResults() {
    $totalCount   = 0;
    $successCount = 0;
    $failCount    = 0;
    foreach ( $this->tests as $test ) {
      $testResult   = $test->getResult();
      $totalCount   = $totalCount + $testResult['total_count'];
      $successCount = $successCount + count( $testResult['success'] );
      $failCount    = $failCount + count( $testResult['fails'] );
    }

    echo "  ------------------------------------------------------------------------------\n";
    if ( $failCount > 0 ) {
      echo "  \e[1m" . $failCount . " TEST(S) FAILED\e[0m\n";
      echo "  Please resolve the issue(s) in order to avoid breakage.\n";
    }
    else {
      echo "  " . $successCount . " TEST(S) PASSED\n";
    }
    echo "  ------------------------------------------------------------------------------\n";

    return $this;
  }

}
