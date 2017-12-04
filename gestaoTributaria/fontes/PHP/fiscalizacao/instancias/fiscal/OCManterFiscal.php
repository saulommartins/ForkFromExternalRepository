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
    * Página oculta do formulário de Fiscal

    * Data de Criação   : 19/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: OCManterFiscal.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO."TFISTipoFiscalizacao.class.php"                                  );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISFiscalFiscalizacao.class.php"                                );

switch ($_REQUEST['stCtrl']) {
    case "incluirAtributoFiscal":
        $boLista = true;
        $arValores = Sessao::read('arValores');
        foreach ($arValores as $key=>$value) {
            #if ($value['cod_tipo'] == $_REQUEST['inTipoFiscalizacao']) { $boLista = false; }
            if ($value['cod_tipo'] == $_REQUEST['stTipoFiscalizacao']) { $boLista = false; }
        }
        if ($boLista == true) {
            $obTipoFiscalizacao = new TFISTipoFiscalizacao();
            $rsTipoFiscalizacao = new RecordSet();
            $obTipoFiscalizacao->setDado( 'cod_tipo',$_REQUEST['stTipoFiscalizacao'] );
            $obTipoFiscalizacao->recuperaPorChave( $rsTipoFiscalizacao );

            if (!($rsTipoFiscalizacao->Eof())) {
                $inCount = sizeof( $arValores );
                $arValores[$inCount]['cod_tipo' ] = $rsTipoFiscalizacao->getCampo( 'cod_tipo'  );
                $arValores[$inCount]['descricao'] = $rsTipoFiscalizacao->getCampo( 'descricao' );
                Sessao::write( 'arValores', $arValores );

                return montaAtributoFiscal( $arValores );
            } else {
                $stMensagem = "@Código do Tipo de Fiscalização inválido (".$_REQUEST['stTipoFiscalizacao'].")   ";
                $js        .= "alertaAviso(".$stMensagem.",'form','erro','".Sessao::getId()."');                  \n";
            }
        } else {
            $stMensagem = "@Tipo de Fiscalização já informada.(".$_REQUEST['stTipoFiscalizacao'].")   ";
            $js        .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');          \n";
        }
        echo $js;
        break;

    case "carregaAtributoFiscal":

        $obTFiscalFiscalizacao = new TFISFiscalFiscalizacao();
        $rsFiscalFiscalizacao  = new RecordSet();

        $stFiltro = " AND fiscal_fiscalizacao.cod_fiscal = ".$_REQUEST['codFiscal']." \n";

        $obTFiscalFiscalizacao->recuperaListaFiscalFiscalizacao($rsFiscalFiscalizacao,$stFiltro);
        $inCount = 0;
        $arValores = Sessao::read('arValores');
        while (!($rsFiscalFiscalizacao->eof())) {
            $arValores[$inCount]['cod_tipo' ] = $rsFiscalFiscalizacao->getCampo('cod_tipo' );
            $arValores[$inCount]['descricao'] = $rsFiscalFiscalizacao->getCampo('descricao');
            $inCount++;
            $rsFiscalFiscalizacao->proximo();
        }
        Sessao::write( 'arValores', $arValores );

        return montaAtributoFiscal($arValores);
        break;

    case 'excluirAtributoFiscal':
        $arAtributoFiscal = array();
        $inCount          = 0;
        $key              = trim($_REQUEST['inId']);
        $arValores = Sessao::read('arValores');
        foreach ($arValores as $value) {
            $keyValue = trim($value['cod_tipo']);
            if ($key != $keyValue) {
                $arAtributoFiscal[$inCount]['cod_tipo' ] = $value['cod_tipo' ];
                $arAtributoFiscal[$inCount]['descricao'] = $value['descricao'];
                $inCount++;
            }
        }

        Sessao::write( 'arValores', $arAtributoFiscal );
        $stJs .= montaAtributoFiscal( $arAtributoFiscal );

        return $stJs;
    break;

}

function montaAtributoFiscal($arValores, $stAcao = '')
{
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arValores );

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Lista de Atribuições');

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth   ( 5        );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth   ( 10       );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth   ( 80          );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Ação" );
    $obLista->ultimoCabecalho->setWidth   ( 10     );
    $obLista->commitCabecalho();

    ////dados

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento( "CENTRO"   );
    $obLista->ultimoDado->setCampo      ( "cod_tipo" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA"  );
    $obLista->ultimoDado->setCampo      ( "descricao" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao  ( "EXCLUIR"                                                 );
    $obLista->ultimaAcao->setFuncao( true                                                      );
    $obLista->ultimaAcao->setLink  ( "javascript: executaFuncaoAjax('excluirAtributoFiscal');" );
    $obLista->ultimaAcao->addCampo ( "","&inId=[cod_tipo]"                                     );
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace( "\n","",$html   );
    $html = str_replace( "  ","",$html   );
    $html = str_replace( "'","\\'",$html );

    $stJs = " d.getElementById('spnListaFiscalizacao').innerHTML  = '';          \n";
    $stJs = "d.getElementById('inTipoFiscalizacao').value='';                          \n";
    $stJs.= "var arTipoFiscalizacao = document.getElementsByName('cmbTipoFiscalizacao'); \n";
    $stJs.= "arTipoFiscalizacao[0].value = ''; \n";
    $stJs.= " d.getElementById('spnListaFiscalizacao').innerHTML  = '".$html."'; \n";
    echo $stJs;
}

?>
