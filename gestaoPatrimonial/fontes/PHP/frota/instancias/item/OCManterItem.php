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
    * Data de Criação: 22/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCManterItem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.12

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaItem.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoItem.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php';

include_once CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php";
include_once CAM_GP_ALM_COMPONENTES."IMontaCatalogoClassificacao.class.php";

# Define o nome dos arquivos PHP
$stPrograma = "ManterItem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

# Se a opção do tipo de cadastro for igual a 1.
function montaFormUnicoItem($obFormulario, $obForm)
{
    $rsItem = new RecordSet;

    if ($_REQUEST['stAcao'] == 'alterar') {
        $obTFrotaItem = new TFrotaItem;
        $obTFrotaItem->setDado('cod_item', $_REQUEST['inCodItem']);
        $obTFrotaItem->recuperaRelacionamento($rsItem);

        // Método que verifica se o ítem não está sendo ou foi utilizado por alguma
        // manutenção ou alguma autorização.
        $obTFrotaItem->recuperaPermissaoAlterarItem( $rsPermissaoAlterar );
        $boPermissaoAlterar = $rsPermissaoAlterar->getCampo('permissao');
    }

    $obBscItem = new IPopUpItem($obForm);
    $obBscItem->setValue( $rsItem->getCampo('descricao') );
    $obBscItem->obCampoCod->setValue( $rsItem->getCampo('cod_item') );

    if ($_REQUEST['stAcao'] == 'alterar') {
        $obBscItem->setLabel( true );
        $obBscItem->setNull ( true );
    } else {
        $obBscItem->setNull ( false );
    }

    $obFormulario->addComponente($obBscItem);
}

# Se a opção do tipo de cadastro for igual a 2.
function montaFormClassificacaoCatalogo($obFormulario)
{
    $obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao;
    $obIMontaCatalogoClassificacao->obIMontaClassificacao->setComboClassificacaoCompleta(true);
    $obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido(false);
    $obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida(true);
    $obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNull(false);
    $obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNaoPermiteManutencao(true);

    # Label informativo.
    $obLblAviso = new Label;
    $obLblAviso->setId     ("stLblAviso");
    $obLblAviso->setName   ("stLblAviso");
    $obLblAviso->setRotulo ("&nbsp;");
    $obLblAviso->setValue  ("<span style='color: #c00; font-weight: bold;'>ATENÇÃO!</span>
                            <br/>Este é um processo em lote que irá adicionar um ou mais itens de manutenção do frota conforme
                            a classificação selecionada.
                            <br clear='clear'/><strong>Observação:</strong>
                            <ul>
                                <li>Todos itens da classificação selecionada ficarão com o mesmo tipo.</li>
                                <li>Serão adicionados somente os itens que ainda não foram incluídos como item de manutenção.</li>
                            </ul>");

    $obFormulario->addComponente($obLblAviso);

    $obIMontaCatalogoClassificacao->geraFormulario($obFormulario);
}

switch ($stCtrl) {

    case 'montaFormCadastro':

        $inCodTipoCadastro = $_REQUEST['inCodTipoCadastro'];
        $stAcao            = $_REQUEST['stAcao'];

        # Caso a ação seja Alterar, monta o form para alteração de somente 1 item.
        if ($stAcao == 'alterar')
            $inCodTipoCadastro = 1;

        if (!empty($inCodTipoCadastro) && !empty($stAcao)) {

            $obForm = new Form;
            $obForm->setAction ($pgProc);
            $obForm->setTarget ("oculto");

            # Definições básicas
            $obFormulario = new Formulario;
            $obFormulario->addForm ( $obForm );

            $rsItem = new RecordSet;

            if ($stAcao == 'alterar') {
                $obTFrotaItem = new TFrotaItem;
                $obTFrotaItem->setDado('cod_item', $_REQUEST['inCodItem']);
                $obTFrotaItem->recuperaRelacionamento($rsItem);

                // Método que verifica se o ítem não está sendo ou foi utilizado por alguma
                // manutenção ou alguma autorização.
                $obTFrotaItem->recuperaPermissaoAlterarItem( $rsPermissaoAlterar );
                $boPermissaoAlterar = $rsPermissaoAlterar->getCampo('permissao');
            }

            $obTFrotaTipoItem = new TFrotaTipoItem;
            $obTFrotaTipoItem->recuperaTodos( $rsTipoItem );

            //instancia um select  para o tipo do item
            $obSlTipoItem = new Select;
            $obSlTipoItem->setName              ( 'slTipoItem' );
            $obSlTipoItem->setRotulo            ( 'Tipo' );
            $obSlTipoItem->setTitle             ( 'Informe o tipo do item.' );
            $obSlTipoItem->setCampoId           ( 'cod_tipo' );
            $obSlTipoItem->setCampoDesc         ( 'descricao' );
            $obSlTipoItem->addOption            ( '','Selecione' );
            $obSlTipoItem->preencheCombo        ( $rsTipoItem );
            $obSlTipoItem->setValue             ( $rsItem->getCampo('cod_tipo') );
            $obSlTipoItem->obEvento->setOnChange("montaParametrosGET('montaCombustivel', 'slTipoItem, obHdPermissaoAlterar');");

            if ($stAcao == 'alterar' && $boPermissaoAlterar == 'false') {
                $obSlTipoItem->setDisabled (true);
                $obSlTipoItem->setLabel    (true);
            } else {
                $obSlTipoItem->setNull(false);
            }

            # Monta o formulário para cadastro de único item.
            if ($inCodTipoCadastro == 1) {
                montaFormUnicoItem($obFormulario, $obForm);
            } elseif ($stAcao != 'alterar' && $inCodTipoCadastro == 2) {
                # Monta o formulário para cadastro de único item.
                montaFormClassificacaoCatalogo($obFormulario);
            }

            $obFormulario->addComponente ( $obSlTipoItem );
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();

            # Adiciona o HTML gerado nas funções no Span do Formulário.
            $stJs = "jQuery('#spnForm').html('".$stHTML."');";

        } else {
            # Limpa os Spans criados no Formulário.
            $stJs  = "jQuery('#spnForm').html('');        \n";
            $stJs .= "jQuery('#spnCombustivel').html(''); \n";
        }

    break;

    case 'montaCombustivel':

        if ($_REQUEST['slTipoItem'] == 1) {
            //recupera os combustiveis
            $obTFrotaCombustivel = new TFrotaCombustivel;
            $obTFrotaCombustivel->recuperaTodos( $rsCombustivel );

            //instancia um select para os combustiveis
            $obSelectCombustivel = new Select();
            $obSelectCombustivel->setName( 'slCombustivel' );
            $obSelectCombustivel->setRotulo( 'Combustível' );
            $obSelectCombustivel->setTitle( 'Informe o combustível do item.' );
            $obSelectCombustivel->addOption( '', 'Selecione' );
            $obSelectCombustivel->setCampoId( 'cod_combustivel' );
            $obSelectCombustivel->setCampoDesc( 'nom_combustivel' );
            $obSelectCombustivel->preencheCombo( $rsCombustivel );
            $obSelectCombustivel->setValue( $_REQUEST['inCodCombustivel'] );
            $obSelectCombustivel->setNull( false );

            // Caso o ítem esteja sendo usado ou foi usado por alguma manutenção
            // ou autorização, exibe somente a informação do campo.
            if ($_REQUEST['obHdPermissaoAlterar'] == "false") {
                $obSelectCombustivel->setLabel( true );
                $obSelectCombustivel->setNull( true );
            }

            //monta um formulario para o combustivel
            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obSelectCombustivel );

            $obFormulario->montaInnerHTML();
            $stJs = "jQuery('#spnCombustivel').html('".$obFormulario->getHTML()."'); \n";
        } else {
            $stJs .= "jQuery('#spnCombustivel').html(''); \n";
        }

    break;
}

echo $stJs;

?>
