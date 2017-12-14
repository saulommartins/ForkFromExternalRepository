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
    * Data de Criação: 28/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25857 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-05 13:01:37 -0300 (Sex, 05 Out 2007) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.2  2007/10/05 16:01:37  hboaventura
inclusão dos arquivos

Revision 1.1  2007/10/05 12:59:35  hboaventura
inclusão dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterTransferirBem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaListaBens($arBens)
{
;

    $rsBens = new RecordSet();
    $rsBens->preenche( $arBens );

    $obTable = new Table();
    $obTable->setRecordset( $rsBens );
    $obTable->setSummary( 'Lista de Bens' );

    $obTable->Head->addCabecalho( 'Localização', 20 );
    $obTable->Head->addCabecalho( 'Bem', 50 );
    $obTable->Head->addCabecalho( 'Baixado', 10 );

    $obTable->Body->addCampo( 'classificacao', 'C' );
    $obTable->Body->addCampo( '[cod_bem] - [descricao]', 'C' );
    $obTable->Body->addCampo( 'baixado', 'C' );

    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_PAT_INSTANCIAS."bem/".$pgOcul."?".Sessao::getId()."&id=%s', 'excluirBem' );", array( 'cod_bem' ) );

    $obTable->montaHTML( true );

    return "$('spnBem').innerHTML = '".$obTable->getHtml()."';";
}

switch ($stCtrl) {
    case 'incluirTransferenciaBem':
        if ($_REQUEST['inCodBemInicio'] == '') {
            $stMensagem = 'Prencha o campo Bem Inicial.';
        }
        if ($_REQUEST['inCodBemInicio'] != '' AND $_REQUEST['inCodBemFim'] != '' AND $_REQUEST['inCodBemInicio'] > $_REQUEST['inCodBemFim']) {
            $stMensagem = 'O campo Bem Final não pode ser superior ao Inicial.';
        }
        if ($stMensagem == '') {
            $obTPatrimonioBem = new TPatrimonioBem();

            //se passar somente o inCodBemFim, faz um between, se nao, traz so um registro
            if ($_REQUEST['inCodBemFim'] != '') {
                $stFiltro = "
                    WHERE bem.cod_bem BETWEEN ".$_REQUEST['inCodBemInicio']." AND ".$_REQUEST['inCodBemFim']."
                ";
            } else {
                $stFiltro = " WHERE  bem.cod_bem = ".$_REQUEST['inCodBemInicio']." ";
            }
            //recupera de acordo com o filtro
            $obTPatrimonioBem->recuperaRelacionamentoTransferencia( $rsBem, $stFiltro );
            //loop para preencher a sessao com os bens selecionados
            $arBem = Sessao::read('bens');
            $inCount = count( $arBem );
            while ( !$rsBem->eof() ) {
                $boRepetido = false;
                if ( is_array( $arBem ) ) {
                    foreach ($arBem as $arTEMP) {
                        if ( $arTEMP['cod_bem'] == $rsBem->getCampo('cod_bem') ) {
                            $arRepetido[] = $arTEMP['cod_bem'];
                            $boRepetido = true;
                        }
                    }
                }
                $arBemAux = array();
                if ( !$boRepetido AND $rsBem->getCampo('status') != 'baixado' ) {
                    $arBemAux[$inCount]['id'] = $inCount;
                    $arBemAux[$inCount]['cod_bem'] = $rsBem->getCampo( 'cod_bem' );
                    $arBemAux[$inCount]['descricao'] = $rsBem->getCampo( 'descricao' );
                    $arBemAux[$inCount]['classificacao'] = $rsBem->getCampo( 'cod_orgao' ).'.'.$rsBem->getCampo( 'cod_unidade' ).'.'.$rsBem->getCampo( 'cod_departamento' ).'.'.$rsBem->getCampo( 'cod_setor').'.'.$rsBem->getCampo( 'cod_local');
                    $arBemAux[$inCount]['baixado'] = $rsBem->getCampo( 'baixado' );

                    $inCount++;
                }
                $rsBem->proximo();
            }
            Sessao::write('bens',$arBemAux);
            //se algum bem selecionado ja estava no array, mostra um aviso
            if ( is_array( $arRepetido ) ) {
                $stJs.= "alertaAviso('Os bens (".implode(',',$arRepetido).") já estão na lista.','form','erro','".Sessao::getId()."');";
            }

            //monta a lista
            $stJs.= "$('inCodBemInicio').value = '';";
            $stJs.= "$('inCodBemFim').value = '';";
            $stJs.= "$('stNomBemInicio').innerHTML = '&nbsp;';";
            $stJs.= "$('stNomBemFim').innerHTML = '&nbsp;';";
            $stJs.= montaListaBens( $arBemAux );
        } else {
            $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
        }
        break;
    case 'excluirBem' :
        $arBem = Sessao::read('bens');
        for ( $i = 0; $i < count( $arBem ); $i++ ) {
            if ($arBem[$i]['cod_bem'] != $_REQUEST['id']) {
                $arTEMP[] = $arBem[$i];
            }
        }

        Sessao::write('bens',$arTEMP);

        $stJs.= montaListaBens( ( count($arTEMP) > 0 ) ? $arTEMP : array() );

        break;
    case 'limparBens' :
        Sessao::remove('bens');
        break;
}

echo $stJs;
