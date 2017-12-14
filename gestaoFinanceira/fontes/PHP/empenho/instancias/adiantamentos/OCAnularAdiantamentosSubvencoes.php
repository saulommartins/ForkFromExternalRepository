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

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Luciano Hoffmann

    * @ignore

    * Casos de uso: uc-02.03.31
*/

/*
$Log$
Revision 1.1  2007/08/10 14:00:50  luciano
movido de lugar

*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include mapeamentos
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoDocumento.class.php"                              );
include_once( TEMP."TEmpenhoItemPrestacaoContas.class.php" );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php" );

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];

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

        $arValores = Sessao::read('arValores');
        $rsRecordSetItemPrestacao      = new RecordSet();
        $obTEmpenhoItemPrestacaoContas = new TEmpenhoItemPrestacaoContas;
        $obTEmpenhoItemPrestacaoContas->setDado('exercicio'   ,Sessao::getExercicio());
        $obTEmpenhoItemPrestacaoContas->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
        $obTEmpenhoItemPrestacaoContas->setDado('cod_empenho' ,$_REQUEST['inCodEmpenho']);
        $obTEmpenhoItemPrestacaoContas->recuperaListagemPrestacao( $rsRecordSetItemPrestacao,$stFiltro );

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
            $js .= "d.getElementById('flTotalPrestacaoContas').innerHTML = '0,00'; ";
        } else {
            $js .= "d.getElementById('stDtPrestacaoContas').value='".$arValores[0]['stDtPrestacaoContas']."';";
            $js .= "d.getElementById('stDataPrestacaoContas').innerHTML='".$arValores[0]['stDtPrestacaoContas']."';";
            $js .= "d.getElementById('flTotalPrestacaoContas').innerHTML = '".number_format($inTotal,2,',','.')."'; ";
        }

        Sessao::write('arValores', $arValores);
        echo $js.montaListaPrestacaoContas($arValores);

    break;

    case 'anular':

        $arTemp = array();

        $arValores = Sessao::read('arValores');
        foreach ($arValores as $arItem) {

            if ($arItem['id'] != $_REQUEST['id']) {
                $arTemp[] = $arItem;
            } else {
                $arValoresAnular = $arItem;
            }

            $arValores = $arTemp;

        }

        foreach ($arValores as $arItem) {
            $nuTotalValor = bcadd($nuTotalValor,str_replace(',','.',str_replace('.','',$arItem['nuValor'])),2);
        }

        $js.= "d.getElementById('flTotalPrestacaoContas').innerHTML = '".number_format($nuTotalValor,2,',','.')."';";

        Sessao::write('arValores', $arValores);
        Sessao::write('arValoresAnular', $arValoresAnular);
        echo $js.montaListaPrestacaoContas($arValores);

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

        $obLista = new Lista;

        $obLista->setTitulo('Itens da Prestação de Contas');
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsNotasFiscais );
        $obLista->addCabecalho();

        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Data Emissão");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Tipo Docto");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Nr.");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Fornecedor");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->ultimoCabecalho->addConteudo("Ação");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stDataDocumento" );
        $obLista->ultimoDado->setTitle( "Nome" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stTipoDocumento" );
        $obLista->ultimoDado->setTitle( "Conta Lançamento" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inNroDocumento" );
        $obLista->ultimoDado->setTitle( "Situação." );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stNomCredor" );
        $obLista->ultimoDado->setTitle( "" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nuValor");
        $obLista->ultimoDado->setTitle( "" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ANULAR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->setLink( "Javascript:executaFuncaoAjax('anular');" );
        $obLista->ultimaAcao->addCampo("&id","id");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        if ($boExecuta) {
            return "d.getElementById('spnListaNotaFiscal').innerHTML = '".$stHTML."';";
        } else {
            return $stHTML;
        }
}
