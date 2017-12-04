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
    * Página Oculto de Lancamento Partida Dobrada
    * Data de Criação   : 19/10/2006

    $Id: OCConsultarAdiantamentosSubvencoes.php 59612 2014-09-02 12:00:51Z gelson $

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Luciano Hoffmann

    * @ignore

    * Casos de uso: uc-02.03.31
*/
//include do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//include mapeamentos
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoDocumento.class.php"                              );
include_once( TEMP."TEmpenhoItemPrestacaoContas.class.php" );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php" );

$stCtrl = $request->get('stCtrl');

$stPrograma = "ManterAdiantamentosSubvencoes";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$js = "";

switch ($_REQUEST['stCtrl']) {

    case 'montaListaPrestacaoContas':
        $inTotal = 0;
        $arValoresTMP = array();
        $rsRecordSetItemPrestacao      = new RecordSet();

        $arValores = Sessao::read('arValores');
        $obTEmpenhoItemPrestacaoContas = new TEmpenhoItemPrestacaoContas;
        $obTEmpenhoItemPrestacaoContas->setDado('exercicio'   ,Sessao::getExercicio());
        $obTEmpenhoItemPrestacaoContas->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
        $obTEmpenhoItemPrestacaoContas->setDado('cod_empenho' ,$_REQUEST['inCodEmpenho']);
        $obTEmpenhoItemPrestacaoContas->recuperaListagemPrestacao( $rsRecordSetItemPrestacao);

        if (!($rsRecordSetItemPrestacao->EOF())) {
            $Cont = 0;

            while (!($rsRecordSetItemPrestacao->EOF())) {

                $obRCGM = new RCGM();
                $obRCGM->setNumCGM($rsRecordSetItemPrestacao->getCampo("credor"));
                $obRCGM->consultar($rsCGM);

                $arValores[$Cont]['id'                 ]=$Cont + 1;
                $arValores[$Cont]['numItem'            ]=$rsRecordSetItemPrestacao->getCampo("num_item"       );
                $arValores[$Cont]['stDtPrestacaoContas']=$rsRecordSetItemPrestacao->getCampo("data"           );
                $arValores[$Cont]['inCodTipoDocumento' ]=$rsRecordSetItemPrestacao->getCampo("cod_documento"  );
                $arValores[$Cont]['stDataDocumento'    ]=$rsRecordSetItemPrestacao->getCampo("data_item"      );
                $arValores[$Cont]['inNroDocumento'     ]=$rsRecordSetItemPrestacao->getCampo("num_documento"  );
                $arValores[$Cont]['inCodFornecedor'    ]=$rsRecordSetItemPrestacao->getCampo("credor"         );
                $arValores[$Cont]['stNomCredor'        ]=$obRCGM->getNomCGM();
                $arValores[$Cont]['stJustificativa'    ]=$rsRecordSetItemPrestacao->getCampo("justificativa"  );
                $arValores[$Cont]['nuValor'            ]=number_format($rsRecordSetItemPrestacao->getCampo("valor_item"     ),2,',','.');
                $arValores[$Cont]['exercicioConta'     ]=$rsRecordSetItemPrestacao->getCampo("exercicio_conta");
                $arValores[$Cont]['inCodContraPartida' ]=$rsRecordSetItemPrestacao->getCampo("conta_contrapartida");
                $arValores[$Cont]['inCodEmpenho'       ]=$rsRecordSetItemPrestacao->getCampo("cod_empenho"    );
                $arValores[$Cont]['inCodEntidade'      ]=$rsRecordSetItemPrestacao->getCampo("cod_entidade"   );

                if($rsRecordSetItemPrestacao->getCampo("justificativa"  ))
                    $arValores[$Cont]['mostraDetalhe']= true;

                $rsRecordSetTipoDocumento = new RecordSet();
                $obTTipoDocumento         = new TEmpenhoTipoDocumento();
                $stFiltro = " WHERE cod_documento = ".$rsRecordSetItemPrestacao->getCampo("cod_documento"  );
                $obTTipoDocumento->recuperaTodos($rsRecordSetTipoDocumento,$stFiltro);
                $arValores[$Cont]['stTipoDocumento'    ]=$rsRecordSetTipoDocumento->getCampo("descricao" );

                $inX     = str_replace(',','.',str_replace('.','',$arValores[$Cont]['nuValor']));
                $inTotal+= $inX;
                $Cont++;
                $rsRecordSetItemPrestacao->proximo();
            }
        }

        if (count($arValores) == 0) {
            include_once TEMP.'TEmpenhoPrestacaoContas.class.php';
            $obTEmpenhoPrestacaoContas = new TEmpenhoPrestacaoContas;
            $stFiltro  = " WHERE tabela.exercicio = ".Sessao::getExercicio()." ";
            $stFiltro .= " AND tabela.cod_entidade = ".$_REQUEST['inCodEntidade']." ";
            $stFiltro .= " AND tabela.cod_empenho = ".$_REQUEST['inCodEmpenho']." ";
            $obTEmpenhoPrestacaoContas->recuperaPrestacaoSemItem($rsPrestacaoContas, $stFiltro);

            $arValoresTMP[0]['stDtPrestacaoContas'] = $rsPrestacaoContas->getCampo("data");
            $inTotalTMP = $rsPrestacaoContas->getCampo("vl_prestar");
        }

        if (count($arValoresTMP) > 0) {
            $js .= "d.getElementById('stDtPrestacaoContas').innerHTML='".$arValoresTMP[0]['stDtPrestacaoContas']."';";
            $js .= "d.getElementById('stDevolucaoIntegral').innerHTML='Sim';";
            $js .= "d.getElementById('flTotalPrestacaoContas').innerHTML = '".number_format($inTotalTMP,2,',','.')."'; ";
        } elseif (count($arValores) == 0) {
            $js .= "d.getElementById('flTotalPrestacaoContas').innerHTML = '0,00'; ";
        } else {
            $js .= "d.getElementById('stDevolucaoIntegral').innerHTML='Não';";
            $js .= "d.getElementById('stDtPrestacaoContas').innerHTML='".$arValores[0]['stDtPrestacaoContas']."';";
            $js .= "d.getElementById('flTotalPrestacaoContas').innerHTML = '".number_format($inTotal,2,',','.')."'; ";
        }

        Sessao::write('arValores', $arValores);
        echo $js.montaListaPrestacaoContas($arValores);

    break;

    case 'montaDetalhesNota':

        $arValores = Sessao::read('arValores');
        foreach ($arValores as $arItem) {
            if ($arItem['id'] == $_REQUEST['id']) {
                echo htmlspecialchars($arItem['stJustificativa'], ENT_QUOTES, 'UTF-8');
            }
        }

    break;
}

function montaListaPrestacaoContas($arRecordSet , $boExecuta = true)
{
    $stPrograma = "ManterAdiantamentosSubvencoes";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsNotasFiscais = new RecordSet;
    if ($arRecordSet != '') {
        $rsNotasFiscais->preenche( $arRecordSet );
    }

    $obTable = new TableTree();
    $obTable->setRecordset( $rsNotasFiscais );
    $obTable->setSummary     ( 'Itens da Prestação de Contas'  );
    $obTable->addCondicionalTree('mostraDetalhe',true);

    $obTable->setArquivo( CAM_GF_EMP_INSTANCIAS . 'adiantamentos/OCConsultarAdiantamentosSubvencoes.php' );

    $stParamAdicionais  = "&stCtrl=montaDetalhesNota";
    $obTable->setComplementoParametros( $stParamAdicionais );
    $obTable->setParametros( array('id') );

    $obTable->Head->addCabecalho( 'Data Emissão'   , 10 );
    $obTable->Head->addCabecalho( 'Tipo Docto'     , 10 );
    $obTable->Head->addCabecalho( 'Nr.'            , 10 );
    $obTable->Head->addCabecalho( 'Fornecedor'    , 30 );
    $obTable->Head->addCabecalho( 'Valor'         , 10 );

    $obTable->Body->addCampo( 'stDataDocumento' , 'C');
    $obTable->Body->addCampo( 'stTipoDocumento' , 'C');
    $obTable->Body->addCampo( 'inNroDocumento'  , 'C');
    $obTable->Body->addCampo( 'stNomCredor'          );
    $obTable->Body->addCampo( 'nuValor'         , 'C');

    $obTable->montaHTML(true);
    $stHTML = $obTable->getHtml();

    if ($boExecuta) {
        return "d.getElementById('spnListaNotaFiscal').innerHTML = '".$stHTML."';";
    } else {
        return $stHTML;
    }
}
