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
    * Página de Processamento de Empenhamento de Despesas Mensais Fixas
    * Data de Criação : 08/09/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 31087 $
    $Name$
    $Autor: $
    $Date: 2007-04-09 13:22:14 -0300 (Seg, 09 Abr 2007) $

    * Casos de uso: uc-02.03.30
*/

/**

$Log$
Revision 1.8  2007/04/09 16:22:14  luciano
#9023#

Revision 1.7  2006/12/05 22:38:59  cleisson
Bug #7710#

Revision 1.6  2006/11/21 15:53:20  cako
Bug #7546#

Revision 1.5  2006/11/16 22:23:42  gelson
Bug #7305#

Revision 1.4  2006/11/16 18:38:34  cako
Bug #7298#

Revision 1.3  2006/11/13 20:04:45  cleisson
Bug #7446#

Revision 1.2  2006/10/21 16:13:24  tonismar
bug #7259#

Revision 1.1  2006/09/26 17:58:01  tonismar
Manter Empenho Despesas Mensais Fixas

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEmpenho";
$pgFormDiverso = "FMManterEmpenhoDiversos.php";
$pgProcDiverso = "PRManterEmpenhoDiversos.php";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJS          = "JS".$stPrograma.".js";

$obREmpenhoEmpenho = new REmpenhoEmpenho();

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$obREmpenhoEmpenho = new REmpenhoEmpenho;

//Atributos Dinâmicos
//-------->
foreach ($arChave as $key=>$value) {
    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
    $inCodAtributo = $arChaves[0];
    if ( is_array($value) ) {
        $value = implode(",",$value);
    }
    $obREmpenhoEmpenho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
}
//<--------

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":

        $stDescricaoEmpenho = "Despesa com ".$_REQUEST['stTipoDespesaFixa'];

        $obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );
        $obREmpenhoEmpenho->setDescricao( $stDescricaoEmpenho );
        $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obREmpenhoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( $_POST['inCodTipoEmpenho'] );
        $obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST['inCodDespesa'] );
        $obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $_REQUEST['stDesdobramento'] );
        $obREmpenhoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
        $obREmpenhoEmpenho->obRCGM->setNumCGM( $_REQUEST['inCodFornecedor'] );
        $obREmpenhoEmpenho->setDtEmpenho( $_REQUEST['stDtEmpenho'] );
        $obREmpenhoEmpenho->setDtVencimento( $_REQUEST['dtVencimento'] );
        $obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_REQUEST['inNumOrgao'] );
        $obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $_REQUEST['inNumUnidade'] );
        $obREmpenhoEmpenho->setCodDespesaFixa( $_REQUEST['inCodDespesaFixa'] );
        $obREmpenhoEmpenho->setCodCategoria( 1 ); // Valor fixo = Categoria Comum
        $obREmpenhoEmpenho->obROrcamentoReservaSaldos->setVlReserva( $_REQUEST['flValorTotalItem'] );
        $stNomItem = "Despesa com ".$_REQUEST['stTipoDespesaFixa']." de ".$_REQUEST['stNomLocal'].", cfe contrato nr. ".$_REQUEST['hdnContrato'].", identificador nr. ".$_REQUEST['stIdentificador'];

        $obErro = new Erro();
        if ( Sessao::read('arItens') ) {
            $arItensSessao = Sessao::read('arItens');
            foreach ($arItensSessao as $arItemPreEmpenho) {

                $stDataEmpenho = implode(array_reverse(explode('/',$_REQUEST['stDtEmpenho'])));
                $stDataItem    = implode(array_reverse(explode('/',$arItemPreEmpenho['data_documento'])));

                if ($stDataItem < $stDataEmpenho) {
                    $obErro->setDescricao("Data do Documento do item ".$arItemPreEmpenho['id']." deve ser maior ou igual a '".$_REQUEST['stDtEmpenho']."'!");
                }

                $obREmpenhoEmpenho->addItemPreEmpenho();
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setNumItem( $arItemPreEmpenho['id'] );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setQuantidade( $arItemPreEmpenho['consumo'] );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setNomUnidade( 'unidade' );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setValorTotal( $arItemPreEmpenho['valor'] );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setNomItem( $stNomItem );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setComplemento( $arItemPreEmpenho['complemento']);
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade(1);
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza(7);
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade('un');
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setDataDocumento( $arItemPreEmpenho['data_documento'] );
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obREmpenhoEmpenho->emitirEmpenhoDespesaFixa();
        }
        if ( !$obErro->ocorreu() ) {
//            SistemaLegado::LiberaFrames(true,False);
            $inSessaoCorrente = session_id();
            session_regenerate_id();
            $inNovaSessao = session_id();
            session_start( $inSessaoCorrente );
//            if ("S" == "S") {
                $pgProx = CAM_GF_EMP_INSTANCIAS."liquidacao/FMManterLiquidacao.php";
                $stFiltroLiquidacao  = "&inCodEmpenho=".$obREmpenhoEmpenho->getCodEmpenho();
                $stFiltroLiquidacao .= "&inCodPreEmpenho=".$obREmpenhoEmpenho->getCodPreEmpenho();
                $stFiltroLiquidacao .= "&inCodEntidade=".$_POST['inCodEntidade'];
                $stFiltroLiquidacao .= "&dtExercicioEmpenho=".Sessao::getExercicio();
                $stFiltroLiquidacao .= "&stEmitirEmpenho=S";
                $stFiltroLiquidacao .= "&stAcaoEmpenho=".$stAcao;
                $stFiltroLiquidacao .= "&pgProxEmpenho=".$pgForm;
                $stFiltroLiquidacao .= "&pgDespesasFixas=FMManterDespesasMensaisFixas.php";
                $stFiltroLiquidacao .= "&acao=812&modulo=10&funcionalidade=202&nivel=1&cod_gestao_pass=2&stNomeGestao=Financeira&modulos=Empenho";
                $stFiltroLiquidacao .= "&acaoEmpenho=822&moduloEmpenho=10&funcionalidadeEmpenho=82";
                print '<script type="text/javascript">
                            mudaMenu         ( "Liquidação","202" );
                       </script>';
                SistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId()."&stAcao=liquidar".$stFiltroLiquidacao, $obREmpenhoEmpenho->getCodEmpenho()."/".Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
/*            } else {
                SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId().$stFiltro, $obREmpenhoEmpenho->getCodEmpenho()."/".Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
            }*/
            session_start( $inNovaSessao );
            Sessao::setId("PHPSESSID=".$inNovaSessao);
            Sessao::write('acao', 822);
            Sessao::geraURLRandomica();
            $stCaminho = CAM_GF_EMP_INSTANCIAS."empenho/OCRelatorioEmpenhoOrcamentario.php";
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodEmpenho=".$obREmpenhoEmpenho->getCodEmpenho(). "&inCodEntidade=" .$_POST['inCodEntidade'];
            SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
        } else {
//            SistemaLegado::executaFrameOculto( "window.parent.frames['telaPrincipal'].document.frm.Ok.disabled = false;" );
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
//            SistemaLegado::LiberaFrames(true,False);
        }

    break;
}
