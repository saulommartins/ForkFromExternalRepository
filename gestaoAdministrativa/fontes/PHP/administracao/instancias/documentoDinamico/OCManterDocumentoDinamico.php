<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Arquivo de instância para manutenção de documentos dinâmicos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3574 $
$Name$
$Author: lizandro $
$Date: 2005-12-07 15:23:54 -0200 (Qua, 07 Dez 2005) $

Casos de uso: uc-01.03.99
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RDocumentoDinamicoDocumento.class.php"     );

$obRDocumentoDinamico = new RDocumentoDinamicoDocumento;

//Define o nome dos arquivos PHP
$stPrograma          = "ManterDocumentoDinamico";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgJs                = "JS".$stPrograma.".js";

include_once( $pgJs );
switch ($_REQUEST ["stCtrl"]) {
    case "mostraBloco":
    $array = array();
    $inNovoIndice = 0;
    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ( "Blocos do documento" );
    $inBloco = $_REQUEST['inBloco'];

    if ($_REQUEST['stRemove']) {
      $inBloco++;
    }

    for ($iCount = 1; $iCount < $inBloco; $iCount++) {

      if ($_REQUEST['stRemove']) {
         if ($iCount != $_REQUEST["stRemove"]) {
              $inNovoIndice++;
              $nomeVar = "obTxtBloco".$inNovoIndice;
              ${"obRdbAlinEsq".$inNovoIndice} = new Radio;
              ${"obRdbAlinEsq".$inNovoIndice}->setRotulo  ( "Alinhamento Bloco ".$inNovoIndice);
              ${"obRdbAlinEsq".$inNovoIndice}->setLabel   ( "Esquerdo" );
              ${"obRdbAlinEsq".$inNovoIndice}->setValue   ( "L"        );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'L') {
                  ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinEsq".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinCen".$inNovoIndice} = new Radio;
              ${"obRdbAlinCen".$inNovoIndice}->setLabel   ( "Centralizado" );
              ${"obRdbAlinCen".$inNovoIndice}->setValue   ( "C"   );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'C') {
                  ${"obRdbAlinCen".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinCen".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinCen".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinDir".$inNovoIndice} = new Radio;
              ${"obRdbAlinDir".$inNovoIndice}->setLabel   ( "Direito" );
              ${"obRdbAlinDir".$inNovoIndice}->setValue   ( "R"   );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'R') {
                  ${"obRdbAlinDir".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinDir".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinDir".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinJus".$inNovoIndice} = new Radio;
              ${"obRdbAlinJus".$inNovoIndice}->setLabel   ( "Justificado" );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'J' || ($_REQUEST['boAlinhamento-'.$iCount])== '') {
                  ${"obRdbAlinJus".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinJus".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinJus".$inNovoIndice}->setValue   ( "J"  );
              ${"obRdbAlinJus".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              $$nomeVar = new TextArea;
              $$nomeVar->setRotulo ( "Bloco ".$inNovoIndice );
              $$nomeVar->setName   ( "stBloco".$inNovoIndice );
              $$nomeVar->setNull   ( false );
              $$nomeVar->setCols   (70);
              $$nomeVar->setRows   (5);
              $$nomeVar->setStyle  ( "width: 580px"  );

              if (($_REQUEST["stBloco".$iCount]) <> '') {
                 $$nomeVar->setValue ($_REQUEST["stBloco".$iCount]);
              }
              $$nomeVar->obEvento->setOnFocus("setControleTextArea(this)");

             $obBtnRemoveBloco = new Button;
             $obBtnRemoveBloco->setName              ( "btRemoveBloco"               );
             $obBtnRemoveBloco->setValue             ( "Remover Bloco"                 );
             $obBtnRemoveBloco->obEvento->setOnClick ( "modificaDado2('removeBloco',(".$inNovoIndice."))" );

             $obBtnInsereBloco = new Button;
             $obBtnInsereBloco->setName              ( "btInsereBloco"               );
             $obBtnInsereBloco->setValue             ( "Incluir Bloco"                 );
             $obBtnInsereBloco->obEvento->setOnClick ( "modificaDado('incluiBloco',(".$inNovoIndice."))" );

             $obFormulario->agrupaComponentes ( array (${"obRdbAlinEsq".$inNovoIndice},${"obRdbAlinCen".$inNovoIndice},${"obRdbAlinDir".$inNovoIndice},${"obRdbAlinJus".$inNovoIndice}));
             $obFormulario->addComponente ( $$nomeVar );

             if ($inNovoIndice>1) {
                $obFormulario->defineBarra           ( array ($obBtnInsereBloco,$obBtnRemoveBloco),"","" );
             } else {
                $obFormulario->defineBarra           ( array ($obBtnInsereBloco),"" );
             }

         }
      } elseif ($_REQUEST['stInclui']) {
         if ($iCount == $_REQUEST["stInclui"]) {
              $inNovoIndice++;
              $nomeVar = "obTxtBloco".$inNovoIndice;

              ${"obRdbAlinEsq".$inNovoIndice} = new Radio;
              ${"obRdbAlinEsq".$inNovoIndice}->setRotulo  ( "Alinhamento Bloco ".$inNovoIndice);
              ${"obRdbAlinEsq".$inNovoIndice}->setLabel   ( "Esquerdo" );
              ${"obRdbAlinEsq".$inNovoIndice}->setValue   ( "L"        );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'L') {
                  ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinEsq".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinCen".$inNovoIndice} = new Radio;
              ${"obRdbAlinCen".$inNovoIndice}->setLabel   ( "Centralizado" );
              ${"obRdbAlinCen".$inNovoIndice}->setValue   ( "C"   );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'C') {
                  ${"obRdbAlinCen".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinCen".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinCen".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinDir".$inNovoIndice} = new Radio;
              ${"obRdbAlinDir".$inNovoIndice}->setLabel   ( "Direito" );
              ${"obRdbAlinDir".$inNovoIndice}->setValue   ( "R"   );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'R') {
                  ${"obRdbAlinDir".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinDir".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinDir".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinJus".$inNovoIndice} = new Radio;
              ${"obRdbAlinJus".$inNovoIndice}->setLabel   ( "Justificado" );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'J' || ($_REQUEST['boAlinhamento-'.$iCount])== '') {
                  ${"obRdbAlinJus".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinJus".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinJus".$inNovoIndice}->setValue   ( "J"  );
              ${"obRdbAlinJus".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              $$nomeVar = new TextArea;
              $$nomeVar->setRotulo ( "Bloco ".$inNovoIndice );
              $$nomeVar->setName   ( "stBloco".$inNovoIndice );
              $$nomeVar->setNull   ( false );
              $$nomeVar->setCols   (70);
              $$nomeVar->setRows   (5);
              $$nomeVar->setStyle  ( "width: 580px"  );

              if (($_REQUEST["stBloco".$iCount]) <> '') {
                 $$nomeVar->setValue ($_REQUEST["stBloco".$iCount]);
              }
              $$nomeVar->obEvento->setOnFocus("setControleTextArea(this)");

             $obBtnRemoveBloco = new Button;
             $obBtnRemoveBloco->setName              ( "btRemoveBloco"               );
             $obBtnRemoveBloco->setValue             ( "Remover Bloco"                 );
             $obBtnRemoveBloco->obEvento->setOnClick ( "modificaDado2('removeBloco',(".$inNovoIndice."))" );

             $obBtnInsereBloco = new Button;
             $obBtnInsereBloco->setName              ( "btInsereBloco"               );
             $obBtnInsereBloco->setValue             ( "Incluir Bloco"                 );
             $obBtnInsereBloco->obEvento->setOnClick ( "modificaDado('incluiBloco',(".$inNovoIndice."))" );

             $obFormulario->agrupaComponentes ( array (${"obRdbAlinEsq".$inNovoIndice},${"obRdbAlinCen".$inNovoIndice},${"obRdbAlinDir".$inNovoIndice},${"obRdbAlinJus".$inNovoIndice}));
             $obFormulario->addComponente ( $$nomeVar );

             if ($inNovoIndice>1) {
                $obFormulario->defineBarra           ( array ($obBtnInsereBloco,$obBtnRemoveBloco),"","" );
             } else {
                $obFormulario->defineBarra           ( array ($obBtnInsereBloco),"" );
             }

//INSERE NOVO BLOCO
              $inNovoIndice++;
              $nomeVar = "obTxtBloco".$inNovoIndice;

              ${"obRdbAlinEsq".$inNovoIndice} = new Radio;
              ${"obRdbAlinEsq".$inNovoIndice}->setRotulo  ( "Alinhamento Bloco ".$inNovoIndice);
              ${"obRdbAlinEsq".$inNovoIndice}->setLabel   ( "Esquerdo" );
              ${"obRdbAlinEsq".$inNovoIndice}->setValue   ( "L"        );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'L') {
                  ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinEsq".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinCen".$inNovoIndice} = new Radio;
              ${"obRdbAlinCen".$inNovoIndice}->setLabel   ( "Centralizado" );
              ${"obRdbAlinCen".$inNovoIndice}->setValue   ( "C"   );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'C') {
                  ${"obRdbAlinCen".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinCen".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinCen".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinDir".$inNovoIndice} = new Radio;
              ${"obRdbAlinDir".$inNovoIndice}->setLabel   ( "Direito" );
              ${"obRdbAlinDir".$inNovoIndice}->setValue   ( "R"   );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'R') {
                  ${"obRdbAlinDir".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinDir".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinDir".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinJus".$inNovoIndice} = new Radio;
              ${"obRdbAlinJus".$inNovoIndice}->setLabel   ( "Justificado" );
              if (($_REQUEST['boAlinhamento-'.$inNovoIndice]) == 'J' || ($_REQUEST['boAlinhamento-'.$in])== '') {
                  ${"obRdbAlinJus".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinJus".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinJus".$inNovoIndice}->setValue   ( "J"  );
              ${"obRdbAlinJus".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              $$nomeVar = new TextArea;
              $$nomeVar->setRotulo ( "Bloco ".$inNovoIndice );
              $$nomeVar->setName   ( "stBloco".$inNovoIndice );
              $$nomeVar->setNull   ( false );
              $$nomeVar->setCols   (70);
              $$nomeVar->setRows   (5);
              $$nomeVar->setStyle  ( "width: 580px"  );

//              if (($_REQUEST["stBloco".$iunt]) <> '') {
              $$nomeVar->setValue ("");
  //            }
              $$nomeVar->obEvento->setOnFocus("setControleTextArea(this)");

             $obBtnRemoveBloco = new Button;
             $obBtnRemoveBloco->setName              ( "btRemoveBloco"               );
             $obBtnRemoveBloco->setValue             ( "Remover Bloco"                 );
             $obBtnRemoveBloco->obEvento->setOnClick ( "modificaDado2('removeBloco',(".$inNovoIndice."))" );

             $obBtnInsereBloco = new Button;
             $obBtnInsereBloco->setName              ( "btInsereBloco"               );
             $obBtnInsereBloco->setValue             ( "Incluir Bloco"                 );
             $obBtnInsereBloco->obEvento->setOnClick ( "modificaDado('incluiBloco',(".$inNovoIndice."))" );

             $obFormulario->agrupaComponentes ( array (${"obRdbAlinEsq".$inNovoIndice},${"obRdbAlinCen".$inNovoIndice},${"obRdbAlinDir".$inNovoIndice},${"obRdbAlinJus".$inNovoIndice}));
             $obFormulario->addComponente ( $$nomeVar );

             if ($inNovoIndice>1) {
                $obFormulario->defineBarra           ( array ($obBtnInsereBloco,$obBtnRemoveBloco),"","" );
             } else {
                $obFormulario->defineBarra           ( array ($obBtnInsereBloco),"" );
             }

//################################
      } else {
              $inNovoIndice++;
              $nomeVar = "obTxtBloco".$inNovoIndice;

              ${"obRdbAlinEsq".$inNovoIndice} = new Radio;
              ${"obRdbAlinEsq".$inNovoIndice}->setRotulo  ( "Alinhamento Bloco ".$inNovoIndice);
              ${"obRdbAlinEsq".$inNovoIndice}->setLabel   ( "Esquerdo" );
              ${"obRdbAlinEsq".$inNovoIndice}->setValue   ( "L"        );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'L') {
                  ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinEsq".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinCen".$inNovoIndice} = new Radio;
              ${"obRdbAlinCen".$inNovoIndice}->setLabel   ( "Centralizado" );
              ${"obRdbAlinCen".$inNovoIndice}->setValue   ( "C"   );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'C') {
                  ${"obRdbAlinCen".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinCen".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinCen".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinDir".$inNovoIndice} = new Radio;
              ${"obRdbAlinDir".$inNovoIndice}->setLabel   ( "Direito" );
              ${"obRdbAlinDir".$inNovoIndice}->setValue   ( "R"   );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'R') {
                  ${"obRdbAlinDir".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinDir".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinDir".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              ${"obRdbAlinJus".$inNovoIndice} = new Radio;
              ${"obRdbAlinJus".$inNovoIndice}->setLabel   ( "Justificado" );
              if (($_REQUEST['boAlinhamento-'.$iCount]) == 'J' || ($_REQUEST['boAlinhamento-'.$iCount])== '') {
                  ${"obRdbAlinJus".$inNovoIndice}->setChecked ( true );
              } else {
                  ${"obRdbAlinJus".$inNovoIndice}->setChecked ( false );
              }
              ${"obRdbAlinJus".$inNovoIndice}->setValue   ( "J"  );
              ${"obRdbAlinJus".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

              $$nomeVar = new TextArea;
              $$nomeVar->setRotulo ( "Bloco ".$inNovoIndice );
              $$nomeVar->setName   ( "stBloco".$inNovoIndice );
              $$nomeVar->setNull   ( false );
              $$nomeVar->setCols   (70);
              $$nomeVar->setRows   (5);
              $$nomeVar->setStyle  ( "width: 580px"  );

              if (($_REQUEST["stBloco".$iCount]) <> '') {
                 $$nomeVar->setValue ($_REQUEST["stBloco".$iCount]);
              }
              $$nomeVar->obEvento->setOnFocus("setControleTextArea(this)");

             $obBtnRemoveBloco = new Button;
             $obBtnRemoveBloco->setName              ( "btRemoveBloco"               );
             $obBtnRemoveBloco->setValue             ( "Remover Bloco"                 );
             $obBtnRemoveBloco->obEvento->setOnClick ( "modificaDado2('removeBloco',(".$inNovoIndice."))" );

             $obBtnInsereBloco = new Button;
             $obBtnInsereBloco->setName              ( "btInsereBloco"               );
             $obBtnInsereBloco->setValue             ( "Incluir Bloco"                 );
             $obBtnInsereBloco->obEvento->setOnClick ( "modificaDado('incluiBloco',(".$inNovoIndice."))" );

             $obFormulario->agrupaComponentes ( array (${"obRdbAlinEsq".$inNovoIndice},${"obRdbAlinCen".$inNovoIndice},${"obRdbAlinDir".$inNovoIndice},${"obRdbAlinJus".$inNovoIndice}));
             $obFormulario->addComponente ( $$nomeVar );

             if ($inNovoIndice>1) {
                $obFormulario->defineBarra           ( array ($obBtnInsereBloco,$obBtnRemoveBloco),"","" );
             } else {
                $obFormulario->defineBarra           ( array ($obBtnInsereBloco),"" );
             }
      }
    }

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML     ();

        }
        $js .= "d.getElementById('spnBloco').innerHTML = '".$stHTML."';\n";
        executaFrameOculto($js);

    break;

case "mostraBlocoBanco":

    $obRDocumentoDinamico->setCodDocumento($_REQUEST['inCodDocumento']);
    $obRDocumentoDinamico->listarDocumentoBlocoTexto($arBlocos);

    $array = array();
    $inNovoIndice = 0;
    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ( "Blocos do documento" );

      foreach ($arBlocos as $obRBloco) {

            $inNovoIndice++;
            $nomeVar = "obTxtBloco".$inNovoIndice;

            ${"obRdbAlinEsq".$inNovoIndice} = new Radio;
            ${"obRdbAlinEsq".$inNovoIndice}->setRotulo  ( "Alinhamento Bloco ".$inNovoIndice);
            ${"obRdbAlinEsq".$inNovoIndice}->setLabel   ( "Esquerdo" );
            ${"obRdbAlinEsq".$inNovoIndice}->setValue   ( "L"        );
            if ($obRBloco->getAlinhamento() == 'L') {
               ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( true );
            } else {
               ${"obRdbAlinEsq".$inNovoIndice}->setChecked ( false );
            }

            ${"obRdbAlinEsq".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

            ${"obRdbAlinCen".$inNovoIndice} = new Radio;
            ${"obRdbAlinCen".$inNovoIndice}->setLabel   ( "Centralizado" );
            ${"obRdbAlinCen".$inNovoIndice}->setValue   ( "C"   );
            if ($obRBloco->getAlinhamento() == 'C') {
               ${"obRdbAlinCen".$inNovoIndice}->setChecked ( true );
            } else {
               ${"obRdbAlinCen".$inNovoIndice}->setChecked ( false );
            }
            ${"obRdbAlinCen".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

            ${"obRdbAlinDir".$inNovoIndice} = new Radio;
            ${"obRdbAlinDir".$inNovoIndice}->setLabel   ( "Direito" );
            ${"obRdbAlinDir".$inNovoIndice}->setValue   ( "R"   );
            if ($obRBloco->getAlinhamento() == 'R') {
               ${"obRdbAlinDir".$inNovoIndice}->setChecked ( true );
            } else {
               ${"obRdbAlinDir".$inNovoIndice}->setChecked ( false );
            }
            ${"obRdbAlinDir".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

            ${"obRdbAlinJus".$inNovoIndice} = new Radio;
            ${"obRdbAlinJus".$inNovoIndice}->setLabel   ( "Justificado" );
            if ($obRBloco->getAlinhamento() == 'J') {
               ${"obRdbAlinJus".$inNovoIndice}->setChecked ( true );
            } else {
               ${"obRdbAlinJus".$inNovoIndice}->setChecked ( false );
            }
            ${"obRdbAlinJus".$inNovoIndice}->setValue   ( "J"  );
            ${"obRdbAlinJus".$inNovoIndice}->setName    ( "boAlinhamento-".$inNovoIndice );

            $$nomeVar = new TextArea;
            $$nomeVar->setRotulo ( "Bloco ".$inNovoIndice );
            $$nomeVar->setName   ( "stBloco".$inNovoIndice );
            $$nomeVar->setNull   ( false );
            $$nomeVar->setCols   (70);
            $$nomeVar->setRows   (5);
            $$nomeVar->setStyle  ( "width: 580px"  );

            $$nomeVar->setValue ($obRBloco->getTexto());
            $$nomeVar->obEvento->setOnFocus("setControleTextArea(this)");

           $obBtnRemoveBloco = new Button;
           $obBtnRemoveBloco->setName              ( "btRemoveBloco"               );
           $obBtnRemoveBloco->setValue             ( "Remover Bloco"                 );
           $obBtnRemoveBloco->obEvento->setOnClick ( "modificaDado2('removeBloco',(".$inNovoIndice."))" );

           $obBtnInsereBloco = new Button;
           $obBtnInsereBloco->setName              ( "btInsereBloco"               );
           $obBtnInsereBloco->setValue             ( "Incluir Bloco"                 );
           $obBtnInsereBloco->obEvento->setOnClick ( "modificaDado('incluiBloco',(".$inNovoIndice."))" );

            $obFormulario->agrupaComponentes ( array (${"obRdbAlinEsq".$inNovoIndice},${"obRdbAlinCen".$inNovoIndice},${"obRdbAlinDir".$inNovoIndice},${"obRdbAlinJus".$inNovoIndice}));
            $obFormulario->addComponente ( $$nomeVar );
            if ($inNovoIndice>1) {
               $obFormulario->defineBarra           ( array ($obBtnInsereBloco,$obBtnRemoveBloco),"","" );
            } else {
               $obFormulario->defineBarra           ( array ($obBtnInsereBloco),"" );
            }

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML     ();

        }
        $js .= "d.getElementById('spnBloco').innerHTML = '".$stHTML."';\n";
        $js .= "f.inBloco.value = ".$inNovoIndice.";\n";
        executaFrameOculto($js);
    break;
}

?>
