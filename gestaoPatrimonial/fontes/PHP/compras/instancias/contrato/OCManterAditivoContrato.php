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

/*
    * Página Oculto para contrato
    * Data de Criação   : 02/10/2008

    * @author Desenvolvedor: Carlos Adriano
    * Casos de uso :
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasContratoCompraDireta.class.php" );
include_once(CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoDocumentosAtributos.class.php" );

$stPrograma = "ManterAditivoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

switch ($_REQUEST['stCtrl']) {

//Inclui itens na listagem de veiculos de publicacao utilizados
    case 'incluirListaVeiculos':
        $arValores = Sessao::read('arValores');
        if ($_REQUEST['inVeiculo'] == '') {
            $stMensagem = 'Preencha o campo Veículo de Publicação!';
        }
        if ($_REQUEST['dtDataPublicacao'] == '') {
            $stMensagem = 'Preencha o campo Data de Publicação!';
        }
        $boPublicacaoRepetida = false;
        if ( is_array( $arValores ) ) {
            foreach ($arValores as $arTEMP) {
                if ($arTEMP['inVeiculo'] == $_REQUEST["inVeiculo"] & $arTEMP['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao']) {
                    $boPublicacaoRepetida = true ;
                    $stMensagem = "Este veículos de publicação já está na lista.";
                }
            }
        }
        if (!$boPublicacaoRepetida AND !$stMensagem) {
            $inCount = sizeof($arValores);
            $arValores[$inCount]['id'             ] = $inCount + 1;
            $arValores[$inCount]['inVeiculo'      ] = $_REQUEST[ "inVeiculo"                  ];
            $arValores[$inCount]['stVeiculo'      ] = $_REQUEST[ "stNomCgmVeiculoPublicadade" ];
            $arValores[$inCount]['dtDataPublicacao' ] = $_REQUEST[ "dtDataPublicacao"             ];
            $arValores[$inCount]['inNumPublicacao' ] = $_REQUEST[ "inNumPublicacao"             ];
            $arValores[$inCount]['stObservacao'   ] = $_REQUEST[ "stObservacao"               ];
            $arValores[$inCount]['inCodCompraDireta' ] = $_REQUEST[ "HdnCodCompraDireta"            ];
        } else {
            echo "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        }

        Sessao::write('arValores', $arValores);

        echo montaListaVeiculos( $arValores);
        $js.="$('HdnCodVeiculo').value ='';";
        $js.="$('inVeiculo').value ='';";
        $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
        $js.="$('inNumPublicacao').value ='';";
        $js.="$('stObservacao').value = '';";
        $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
        $js.="$('incluiVeiculo').value = 'Incluir';";
        $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
        echo $js;
    break;

    //Carrega itens da listagem de veiculos de publicacao utilizados em seus determinados campos no Form.
    case 'alterarListaVeiculos':
        $i = 0;

        $arValores = Sessao::read('arValores');
        if ( is_array($arValores)) {
            foreach ($arValores as $key => $value) {
                if (($key+1) == $_REQUEST['id']) {
                    $js ="$('HdnCodVeiculo').value                      ='".$_REQUEST['id']."';                         ";
                    $js.="$('inVeiculo').value                          ='".$arValores[$i]['inVeiculo']."';             ";
                    $js.="$('dtDataPublicacao').value                   ='".$arValores[$i]['dtDataPublicacao']."';      ";
                    $js.="$('inNumPublicacao').value                    ='".$arValores[$i]['inNumPublicacao']."';       ";
                    $js.="$('stObservacao').value                       ='".$arValores[$i]['stObservacao']."';          ";
                    $js.="$('stNomCgmVeiculoPublicadade').innerHTML='".$arValores[$i]['stVeiculo']."';                  ";
                    $js.="$('incluiVeiculo').value    ='Alterar';                                                        ";
                    $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'alteradoListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta, HdnCodVeiculo\')');";
                }
                $i++;
            }
        }
        echo $js;
    break;

    //Confirma itens alterados da listagem de veiculos de publicacao utilizados
    case "alteradoListaVeiculos":
         $inCount = 0;
         $boDotacaoRepetida = false;
         $arValores = Sessao::read('arValores');
         foreach ($arValores as $key=>$value) {
            if ($value['inVeiculo'] == $_REQUEST["inVeiculo"] & $value['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao'] AND ( $key+1 != $_REQUEST['HdnCodVeiculo'] ) ) {
                $boDotacaoRepetida = true ;
                break;
            }
         }
         if (!$boDotacaoRepetida) {
               foreach ($arValores as $key=>$value) {
                if (($key+1) == $_REQUEST['HdnCodVeiculo']) {
                  $arValores[$inCount]['id'            ] = $inCount + 1;
                  $arValores[$inCount]['inVeiculo'     ] = $_REQUEST[ "inVeiculo"                  ];
                  $arValores[$inCount]['stVeiculo'     ] = sistemaLegado::pegaDado('nom_cgm','sw_cgm',' WHERE numcgm = '.$_REQUEST['inVeiculo'].' ');
                  $arValores[$inCount]['dtDataPublicacao'] = $_REQUEST[ "dtDataPublicacao"         ];
                  $arValores[$inCount]['inNumPublicacao']  = $_REQUEST[ "inNumPublicacao"          ];
                  $arValores[$inCount]['stObservacao'  ]   = $_REQUEST[ "stObservacao"             ];
                }
                 $inCount++;
               }
               Sessao::write('arValores', $arValores);
               $js.=montaListaVeiculos($arValores);
               $js.="$('HdnCodVeiculo').value ='';";
               $js.="$('inVeiculo').value ='';";
               $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
               $js.="$('inNumPublicacao').value ='';";
               $js.="$('stObservacao').value = '';";
               $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
               $js.="$('incluiVeiculo').value = 'Incluir';";
               $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
               echo $js;

        } else {
           echo "alertaAviso('Este item já consta na listagem dessa publicação.','form','erro','".Sessao::getId()."');";
        }
    break;

    //Exclui itens da listagem de veiculos de publicacao utilizados
    case 'excluirListaVeiculos':

        $boDotacaoRepetida = false;
        $arTEMP            = array();
        $inCount           = 0;
        $arValores = Sessao::read('arValores');
        foreach ($arValores as $key => $value) {
            if (($key+1) != $_REQUEST['id']) {
                $arTEMP[$inCount]['id'               ] = $inCount + 1;
                $arTEMP[$inCount]['inVeiculo'        ] = $value[ "inVeiculo"      ];
                $arTEMP[$inCount]['stVeiculo'        ] = $value[ "stVeiculo"      ];
                $arTEMP[$inCount]['dtDataPublicacao' ] = $value[ "dtDataPublicacao" ];
                $arTEMP[$inCount]['inNumPublicacao'  ] = $value[ "inNumPublicacao"  ];
                $arTEMP[$inCount]['stObservacao'     ] = $value[ "stObservacao"   ];
                $arTEMP[$inCount]['inCodCompraDireta'   ] = $value[ "inCodCompraDireta" ];
                $inCount++;
            }
        }
        Sessao::write('arValores', $arTEMP);
        echo montaListaVeiculos($arTEMP);
     break;

    case 'limparVeiculo' :
        $js.="$('HdnCodVeiculo').value ='';";
        $js.="$('inVeiculo').value ='';";
        $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
        $js.="$('inNumPublicacao').value ='';";
        $js.="$('stObservacao').value = '';";
        $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
        $js.="$('incluiVeiculo').value = 'Incluir';";
        $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
        echo $js;
    break;

    //Carrega itens vazios na listagem de veiculos de publicacao utilizados no carregamento do Form.
    case 'carregaListaVeiculos' :
        $arValores = Sessao::read('arValores');
        echo montaListaVeiculos($arValores);
    break;
}

function montaListaVeiculos($arRecordSet , $boExecuta = true)
{
    if (is_array($arRecordSet)) {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );

        $table = new Table();
        $table->setRecordset   ( $rsRecordSet  );
        $table->setSummary     ( 'Veículos de Publicação'  );

        $table->Head->addCabecalho( 'Veículo de Publicação' , 40  );
        $table->Head->addCabecalho( 'Data', 10  );
        $table->Head->addCabecalho( 'Número Publicação', 12  );
        $table->Head->addCabecalho( 'Observação'     , 40  );

        $table->Body->addCampo( '[inVeiculo]-[stVeiculo] ' , 'E');
        $table->Body->addCampo( 'dtDataPublicacao' );
        $table->Body->addCampo( 'inNumPublicacao' );
        $table->Body->addCampo( 'stObservacao'  );

        $table->Body->addAcao( 'alterar' ,  'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )' , array( 'alterarListaVeiculos', 'id' ) );
        $table->Body->addAcao( 'excluir' ,  'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )' , array( 'excluirListaVeiculos', 'id' ) );

        $table->montaHTML( true );

        if ($boExecuta) {
            return "d.getElementById('spnListaVeiculos').innerHTML = '".$table->getHTML()."';";
        } else {
            return $this->getHTML();
        }
    }
}
