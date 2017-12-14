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
    * Página oculta do formulário de Baixa de Notas Fiscais

    * Data de Criação   : 31/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: OCManterBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.04
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO."TFISBaixaNotas.class.php"                                        );

switch ($_REQUEST['stCtrl']) {
    case "incluirAtributoNotas":
        $boLista = true;
        $arValores = Sessao::read('arValores');
        foreach ($arValores as $key=>$value) {
            if ($value['nr_nota'] == $_REQUEST['inCodNotaFiscal']) { $boLista = false; }
        }

        if ($boLista==true) {
            $rsBaixaNotas  = new RecordSet();
            $obTBaixaNotas = new TFISBaixaNotas();

            $stFiltro = "   AND baixa_notas.nr_nota               = ".$_REQUEST['inCodNotaFiscal']." \n";
            $stFiltro.= "   AND baixa_autorizacao.cod_autorizacao = ".$_REQUEST['cod_autorizacao']." \n";

            $obTBaixaNotas->recuperaListaBaixaNotas( $rsBaixaNotas, $stFiltro );

            if ($rsBaixaNotas->eof()) {
                $inCount = sizeof( $arValores );
                $arValores[$inCount]['nr_nota'     ] = $_REQUEST['inCodNotaFiscal'   ];
                $arValores[$inCount]['inutilizacao'] = $_REQUEST['inCodInutilizacao' ]." - ";
                $arValores[$inCount]['inutilizacao'].= $_REQUEST['stTipoInutilizacao'];
                Sessao::write( 'arValores', $arValores );

                return montaAtributoNotas( $arValores );
            } else {
                echo "alertaAviso('Nota já baixada.(".$inCodNotaFiscal.")','aviso','erro','".Sessao::getId()."','../');";
            }
        } else {
            echo "alertaAviso('Nota já informada.(".$inCodNotaFiscal.")','aviso','aviso','".Sessao::getId()."','../');";
        }
        break;
    case 'excluirAtributoNotas':
        $arAtributoFiscal = array();
        $inCount          = 0;
        $key              = trim($_REQUEST['inId']);
        $arValores = Sessao::read('arValores');
        foreach ($arValores as $value) {
            $keyValue = trim($value['nr_nota']);
            if ($key != $keyValue) {
                $arAtributoFiscal[$inCount]['nr_nota' ]     = $value['nr_nota'     ];
                $arAtributoFiscal[$inCount]['inutilizacao'] = $value['inutilizacao'];
                $inCount++;
            }
        }

        Sessao::write( 'arValores', $arAtributoFiscal );
        $stJs .= montaAtributoNotas( $arAtributoFiscal );

        return $stJs;
    break;
}

function montaAtributoNotas($arValores, $stAcao = '')
{
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arValores );

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Lista de Notas');

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth   ( 5        );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nota" );
    $obLista->ultimoCabecalho->setWidth   ( 10     );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo de Inutilização" );
    $obLista->ultimoCabecalho->setWidth   ( 80                     );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Ação" );
    $obLista->ultimoCabecalho->setWidth   ( 10     );
    $obLista->commitCabecalho();

    ////dados

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento( "CENTRO"  );
    $obLista->ultimoDado->setCampo      ( "nr_nota" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA"     );
    $obLista->ultimoDado->setCampo      ( "inutilizacao" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao  ( "EXCLUIR"                                                );
    $obLista->ultimaAcao->setFuncao( true                                                     );
    $obLista->ultimaAcao->setLink  ( "javascript: executaFuncaoAjax('excluirAtributoNotas');" );
    $obLista->ultimaAcao->addCampo ( "","&inId=[nr_nota]"                                     );
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace( "\n","",$html   );
    $html = str_replace( "  ","",$html   );
    $html = str_replace( "'","\\'",$html );

    $stJs.= " f.inCodInutilizacao.value                          = '';          \n";
    $stJs.= " f.cmbCodInutilizacao.options.selectedIndex         = 0;           \n";
    $stJs.= " f.inCodNotaFiscal.value                            = '';          \n";
    $stJs.= " d.getElementById('spnListaInutilizacao').innerHTML = '".$html."'; \n";
    echo $stJs;
}
