<?php

return CMap::mergeArray(require(__DIR__.'/main.php'), [
                                                          'runtimePath' => CAP_PATH,
                                                          'import'      => [
                                                              'application.components.phar.*',
                                                          ],
                                                          'components'  => [
                                                              'assetManager' => [
                                                                  'class'    => 'PharAssetManager',
                                                                  'basePath' => CAP_PATH.DIRECTORY_SEPARATOR."assets",
                                                              ],
                                                              'urlManager'   => [
                                                                  'rules' => [
                                                                      'assets/<path:(.*)>' => 'asset/default',
                                                                  ],
                                                              ],
                                                              'session'      => [
                                                                  'savePath' => CAP_PATH.DIRECTORY_SEPARATOR.'sessions',
                                                              ],
                                                              'request'      => [
                                                                  'class' => 'PharHttpRequest',
                                                              ],
                                                          ],
                                                      ]);