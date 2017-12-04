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
    * Formulario de Convenio
    * Data de Criação   : 03/10/2006

    * @author Analista:
    * @author Desenvolvedor:  Lucas Teixeira Stephanou
    * @ignore

    $Id: OCManterConvenios.php 34658 2008-10-20 17:01:40Z leonard $

    *Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once ( TLIC . "TLicitacaoPublicacaoConvenio.class.php" );
include_once ( TLIC."TLicitacaoPublicacaoConvenioAditivo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenios";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_LIC_INSTANCIAS."convenios/";

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
        $table->Body->addCampo( '_stObservacao'  );

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

$inNumConvenio = $_REQUEST['inNumConvenio'];

switch ($_REQUEST['stCtrl']) {

    case 'LimparSessao' :
        Sessao::remove('participantes');
        Sessao::remove('rsVeiculos');
        break;

    case 'IncluirVeiculo' :
        $rsVeiculos = Sessao::read('rsVeiculos');
        $inNumCgmVeiculoPublicidade = $_REQUEST[ 'inCgmVeiculoPublicidade' ];
        if ($inNumCgmVeiculoPublicidade != "") {
            if ( ($boRescindir) && ($_REQUEST["dtPublicacaoRescisao"] == "") ) {
                $stErro = "@Data de Publicação não informada!";
                $boFoco = true;
            } else {
                if ($rsVeiculos ==  null) {
                    $rsVeiculos = new Recordset;
                } else {
                    while ( !$rsVeiculos->eof() ) {
                        if ( $rsVeiculos->getCampo('inCgmVeiculoPublicidade') == $inNumCgmVeiculoPublicidade ) {
                            $stErro = "@Veiculo de Publicidade ja incluido!";
                        }
                        $rsVeiculos->proximo();
                    }
                    $rsVeiculos->setPrimeiroElemento();
                }
            }
        } else {
            $stErro = "@Veículo de Publicidade deve ser informado!";
            $boFoco = true;
        }
        if ($stErro) {
            echo "alertaAviso('" . $stErro . "','form','erro','".Sessao::getId()."');\n";
            if ( $boFoco ) echo " setTimeout('document.getElementById(\'inCgmVeiculoPublicidade\').focus();',400);\n";
        } else {
            // limpar campos
            $stJs  = "d.getElementById('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;'; \n";
            $stJs .= "f.inCgmVeiculoPublicidade.value = '' ;\n";

            /* campos recisão convenio */
            $stJs .= "var dtPublicacaoRescisao = d.getElementById('dtPublicacaoRescisao');";
            $stJs .= "if ( dtPublicacaoRescisao ) dtPublicacaoRescisao.value = '';";
            $stJs .= "var stObsPublicacao = d.getElementById('stObsPublicacao');";
            $stJs .= "if ( stObsPublicacao ) stObsPublicacao.value = '';";
            $stJs .= "var dtRescisao = d.getElementById('dtRescisao');";
            $stJs .= "if ( dtRescisao ) dtRescisao.readOnly = true;\n";

            // buscar cgm
            require_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php");
            $obCgm = new RCGM;
            $obCgm->setNumCGM ( $inNumCgmVeiculoPublicidade );
            $obCgm->consultar ( new Recordset );
            $stNomCgm = $obCgm->getNomCGM();
            unset ( $obCgm );

            // cria array a ser inserido
            if ($boRescindir) {
                $arVeiculo = array  (
                                    'inCgmVeiculoPublicidade' => $inNumCgmVeiculoPublicidade ,
                                    'stNomCgmVeiculoPublicadade' => $stNomCgm ,
                                    'dtPublicacaoRescisao' => $_REQUEST["dtPublicacaoRescisao"] ,
                                    'stObsPublicacao' => $_REQUEST["stObsPublicacao"]
                                );

            } else {
                $arVeiculo = array  (
                                    'inCgmVeiculoPublicidade' => $inNumCgmVeiculoPublicidade ,
                                    'stNomCgmVeiculoPublicadade' => $stNomCgm
                                );
            }

            $rsVeiculos->add ( $arVeiculo );
            $rsVeiculos->ordena ( 'stNomCgmVeiculoPublicadade' );
            Sessao::write('rsVeiculos',$rsVeiculos);
            echo montaListaVeiculos (  $rsVeiculos , $stJs, $boRescindir);

        }
        break;

    case 'limpaVeiculo' :
        $stJs  = "d.getElementById('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;'; \n";
        $stJs .= "f.inCgmVeiculoPublicidade.value = '';";
        $stJs .=  "\n var dtPublicacaoRescisao = d.getElementById('dtPublicacaoRescisao');"
                 ."\n if ( dtPublicacaoRescisao ) dtPublicacaoRescisao.value = '';"
                 ."\n var stObsPublicacao = d.getElementById('stObsPublicacao');"
                 ."\n if ( stObsPublicacao ) stObsPublicacao.value = '';";
        echo $stJs;
        break;

    case 'excluirVeiculo':
        $rsVeiculos = Sessao::read('rsVeiculos');
        $arVeiculos = $rsVeiculos->arElementos;
        $arNovo = array();
        $numcgmExcluir = $_REQUEST['inCgmVeiculoPublicidade'];
        foreach ($arVeiculos as $valor) {
            if ($valor[ 'inCgmVeiculoPublicidade' ] != $numcgmExcluir) {
                $arNovo[] = $valor;
            }
        }

        /*
            Libera o campo, pois quando a acao é rescindir, há o campo data de publicacao
            nos dados a serem adicionados na lista de veículos de publicidade.
            Após a primeira insersão na listagem, o campo data de rescisão é
            passado para readOnly, assim a data não pode ser mais alterada, não tendo
            problemas de alteração de data. O campo é volta ao normal quando a listagem
            ficar fazia.
        */
        if ( empty($arNovo) ) {
            echo "if (d.getElementById('dtRescisao')) {";
            echo "  d.getElementById('dtRescisao').readOnly = false;";
            echo "}";
        }

        $rsVeiculos = new Recordset;
        $rsVeiculos->preenche ( $arNovo );
        $rsVeiculos->ordena ( 'stNomCgmVeiculoPublicadade' );
        Sessao::write('rsVeiculos',$rsVeiculos);
        echo montaListaVeiculos ( $rsVeiculos );
        break;

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
            $arValores[$inCount]['id'             ] 	= $inCount + 1;
            $arValores[$inCount]['inVeiculo'      ] 	= $_REQUEST[ "inVeiculo" ];
            $arValores[$inCount]['stVeiculo'      ] 	= $_REQUEST[ "stNomCgmVeiculoPublicadade" ];
            $arValores[$inCount]['dtDataPublicacao' ] 	= $_REQUEST[ "dtDataPublicacao" ];
            $arValores[$inCount]['inNumPublicacao' ] 	= $_REQUEST[ "inNumPublicacao" ];
            $arValores[$inCount]['_stObservacao'   ] 	= $_REQUEST[ "_stObservacao" ];
            $arValores[$inCount]['inCodCompraDireta' ] 	= $_REQUEST[ "HdnCodCompraDireta" ];
        } else {
            echo "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        }

        Sessao::write('arValores', $arValores);

        echo montaListaVeiculos( $arValores);
        $js.="$('HdnCodVeiculo').value ='';";
        $js.="$('inVeiculo').value ='';";
        $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
    $js.="$('inNumPublicacao').value ='';";
        $js.="$('_stObservacao').value = '';";
        $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
        $js.="$('incluiVeiculo').value = 'Incluir';";
        $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
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
            $js.="$('_stObservacao').value                      ='".$arValores[$i]['_stObservacao']."';         ";
            $js.="$('stNomCgmVeiculoPublicadade').innerHTML='".$arValores[$i]['stVeiculo']."';                  ";
            $js.="$('incluiVeiculo').value    ='Alterar';                                                       ";
            $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'alteradoListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta, HdnCodVeiculo\')');";
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
              $arValores[$inCount]['id']               = $inCount + 1;
              $arValores[$inCount]['inVeiculo']        = $_REQUEST[ "inVeiculo" ];
              $arValores[$inCount]['stVeiculo']        = sistemaLegado::pegaDado('nom_cgm','sw_cgm',' WHERE numcgm = '.$_REQUEST['inVeiculo'].' ');
              $arValores[$inCount]['dtDataPublicacao'] = $_REQUEST[ "dtDataPublicacao" ];
              $arValores[$inCount]['inNumPublicacao']  = $_REQUEST[ "inNumPublicacao" ];
              $arValores[$inCount]['_stObservacao']    = $_REQUEST[ "_stObservacao" ];
            }
             $inCount++;
           }

                   Sessao::write('arValores', $arValores);
           $js.=montaListaVeiculos($arValores);
           $js.="$('HdnCodVeiculo').value ='';";
           $js.="$('inVeiculo').value ='';";
           $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
                   $js.="$('inNumPublicacao').value ='';";
           $js.="$('_stObservacao').value = '';";
           $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
           $js.="$('incluiVeiculo').value = 'Incluir';";
           $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
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
            $arTEMP[$inCount]['_stObservacao'     ] = $value[ "_stObservacao"   ];
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
        $js.="$('_stObservacao').value = '';";
        $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
        $js.="$('incluiVeiculo').value = 'Incluir';";
        $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
        echo $js;
    break;

    case 'carregaListaVeiculos':
        //recupera os veiculos de publicacao, coloca na sessao e manda para o oculto
        $obTLicitacaoPublicacaoConvenioAditivo = new TLicitacaoPublicacaoConvenioAditivo();
        $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'num_aditivo'  , $_REQUEST['inNumeroAditivo']);
        $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'num_convenio' , $_REQUEST['inNumConvenio']);
        $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'exercicio'    , $_REQUEST['stExercicio']);
        $obTLicitacaoPublicacaoConvenioAditivo->recuperaVeiculosPublicacao( $rsVeiculosPublicacao );

        $inCount = 0;
        $arValores = array();
        while ( !$rsVeiculosPublicacao->eof() ) {
            $arValores[$inCount]['id'            ]   = $inCount + 1;
            $arValores[$inCount]['inVeiculo'     ]   = $rsVeiculosPublicacao->getCampo( 'num_veiculo' );
            $arValores[$inCount]['stVeiculo'     ]   = $rsVeiculosPublicacao->getCampo( 'nom_veiculo' );
            $arValores[$inCount]['dtDataPublicacao'] = $rsVeiculosPublicacao->getCampo( 'dt_publicacao' );
            $arValores[$inCount]['inNumPublicacao']  = $rsVeiculosPublicacao->getCampo( 'num_publicacao' );
            $arValores[$inCount]['_stObservacao'  ]  = $rsVeiculosPublicacao->getCampo( 'observacao' );
            $inCount++;

            $rsVeiculosPublicacao->proximo();
        }

        Sessao::write('arValores', $arValores);
        $stJs = montaListaVeiculos ( $arValores );
        echo $stJs;

    break;

    case 'montaBuscaNorma':
         if($_REQUEST['inCodLei'] ){
            include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"  );
            $obTNorma = new TNorma;
            $obTNorma->setDado('cod_norma' , $_REQUEST['inCodLei']);
            $obTNorma->recuperaPorChave($rsNormaAlteracao);

            $stJs .= "jq('#inCodLei').val('".$_REQUEST['inCodLei']."');\n";
            $stJs .= "jq('#stFundamentacaoLegal').html('".$rsNormaAlteracao->getCampo('nom_norma')."');";
            $stJs .= "jq('#stDataNorma').html('".$rsNormaAlteracao->getCampo('dt_assinatura')."');";
            }else{
                $stJs .= "jq('#inCodLei').val('');\n";
                $stJs .= "jq('#stFundamentacaoLegal').html('&nbsp;');";
                $stJs .= "jq('#stDataNorma').html('&nbsp;');";
            }
        echo $stJs;
        
    break;
}
