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
 * Página de Formulário para Filtragem de Ação.
 * Data de Criacao: 04/08/2008

 * @author Analista     : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id$

 * Casos de uso: uc-02.09.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';

$stAcao = $request->get('stAcao');

# Define o nome dos arquivos PHP
$stPrograma = 'ManterAcao';
$pgFilt     = 'FL' . $stPrograma . '.php';
$pgList     = 'LS' . $stPrograma . '.php';
$pgForm     = 'FM' . $stPrograma . '.php';
$pgProc     = 'PR' . $stPrograma . '.php';
$pgOcul     = 'OC' . $stPrograma . '.php';
$pgJs       = 'JS' . $stPrograma . '.php';

# Define form
$obForm = new Form();
$obForm->setAction($pgList);
$obForm->setTarget('telaPrincipal');

# Define campos escondidos
$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnExibePPA = new Hidden();
$obHdnExibePPA->setName('boExibePPA');
$obHdnExibePPA->setValue($_REQUEST['boExibePPA']);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);

# Define popup de ppa
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setNull(true);

$obIPopUpPrograma = new BuscaInner($obForm);
$obIPopUpPrograma->setRotulo('Programa');
$obIPopUpPrograma->setTitle('Informe o programa.');
$obIPopUpPrograma->setId('stNomPrograma');
$obIPopUpPrograma->obCampoCod->setId('inCodPrograma');
$obIPopUpPrograma->obCampoCod->setName('inCodPrograma');
$obIPopUpPrograma->obCampoCod->setSize(10);
$obIPopUpPrograma->obCampoCod->setMaxLength(9);
$obIPopUpPrograma->obCampoCod->setAlign('left');
$obIPopUpPrograma->obCampoCod->setMascara('9999');
$obIPopUpPrograma->obCampoCod->setPreencheComZeros('E');
$stFuncaoBusca = "
    abrePopUp('".CAM_GF_PPA_POPUPS."programa/FLProcurarPrograma.php','".$obForm->getName()."','".$obIPopUpPrograma->obCampoCod->getName()."','".$obIPopUpPrograma->getId()."','&inCodPPA='+jq('#inCodPPATxt').val()+'&','".Sessao::getId()."','800','550');
";
$obIPopUpPrograma->setFuncaoBusca($stFuncaoBusca);
$stOnChange = "
    ajaxJavaScriptSincrono( '".CAM_GF_PPA_POPUPS.'programa/OCProcurarPrograma.php?'.Sessao::getId()."&stNomCampoCod=".$obIPopUpPrograma->obCampoCod->getName()."&stIdCampoDesc=".$obIPopUpPrograma->getId()."&stNomForm=".$obForm->getName()."&inCodPPA='+jq('#inCodPPATxt').val()+'&inNumPrograma='+this.value, 'buscaPrograma' );
";
$obIPopUpPrograma->obCampoCod->obEvento->setOnBlur($stOnChange);
$obIPopUpPrograma->setNull(true);
$obIPopUpPrograma->obCampoCod->setNull(true);

$obRadTipoOrcamentaria = new Radio();
$obRadTipoOrcamentaria->setId('inCodTipoAcao');
$obRadTipoOrcamentaria->setName('inCodTipoAcao');
$obRadTipoOrcamentaria->setRotulo('Tipo da Ação');
$obRadTipoOrcamentaria->setLabel('Orçamentária');
$obRadTipoOrcamentaria->setValue(1);
$obRadTipoOrcamentaria->setNull(true);
$obRadTipoOrcamentaria->obEvento->setOnChange("montaParametrosGET('preencheTipoAcao','inCodTipoAcao');");

$obRadTipoNaoOrcamentaria = new Radio();
$obRadTipoNaoOrcamentaria->setId('inCodTipoAcao');
$obRadTipoNaoOrcamentaria->setName('inCodTipoAcao');
$obRadTipoNaoOrcamentaria->setRotulo('Tipo da Ação');
$obRadTipoNaoOrcamentaria->setLabel('Não Orçamentária');
$obRadTipoNaoOrcamentaria->setValue(2);
$obRadTipoNaoOrcamentaria->setNull(true);
$obRadTipoNaoOrcamentaria->obEvento->setOnChange("montaParametrosGET('preencheTipoAcao','inCodTipoAcao');");

$obSlSubTipoAcao = new Select;
$obSlSubTipoAcao->setName('inCodTipo');
$obSlSubTipoAcao->setId  ('inCodTipo');
$obSlSubTipoAcao->setRotulo('Subtipo da Ação');
$obSlSubTipoAcao->setTitle('Selecione o subtipo da ação');
$obSlSubTipoAcao->addOption('','Selecione');
$obSlSubTipoAcao->setNull(true);


//So para estados de AL e TO
$inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
//Estado de AL = 2
//Estado de TO = 27
if ( ($inCodUf == 2) || ($inCodUf == 27)) {
    $obSlIdentificadorAcao = new Select;
    $obSlIdentificadorAcao->setName('inCodIdentificadorAcao');
    $obSlIdentificadorAcao->setId  ('inCodIdentificadorAcao');
    $obSlIdentificadorAcao->setRotulo('Identificador');
    $obSlIdentificadorAcao->setTitle('Selecione o Identificador da ação');
    $obSlIdentificadorAcao->setNull(true);
    $obSlIdentificadorAcao->addOption('','Selecione');
    //Estado de AL = 2
    if ( $inCodUf == 2 ) {
        include_once CAM_GF_PPA_MAPEAMENTO.'TTCEALIdentificadorAcao.class.php';
        $obTTCEALIdentificadorAcao = new TTCEALIdentificadorAcao();
        $obTTCEALIdentificadorAcao->recuperaTodos($rsIdentificador,"","",$boTransacao);
        //Carrega dados para a select
        while (!$rsIdentificador->eof()) {
            $obSlIdentificadorAcao->addOption($rsIdentificador->getCampo('cod_identificador'),$rsIdentificador->getCampo('descricao'));
            $rsIdentificador->proximo();
        }
    }
    //Estado de TO = 27
    if ( $inCodUf == 27 ) {
        include_once CAM_GF_PPA_MAPEAMENTO.'TTCETOIdentificadorAcao.class.php';
        $obTTCETOIdentificadorAcao = new TTCETOIdentificadorAcao();
        $obTTCETOIdentificadorAcao->recuperaTodos($rsIdentificador,"","",$boTransacao);
        //Carrega dados para a select
        while (!$rsIdentificador->eof()) {
            $obSlIdentificadorAcao->addOption($rsIdentificador->getCampo('cod_identificador'),$rsIdentificador->getCampo('cod_identificador').' - '.$rsIdentificador->getCampo('descricao'));
            $rsIdentificador->proximo();
        }
    }    
}//fim IF Identificador Acao

# Define label de intervalo.
$obLblIntervalo = new Label();
$obLblIntervalo->setValue(' até ');

# Define intervalo inicial da ação
$obTxtAcaoInicio = new TextBox();
$obTxtAcaoInicio->setName('inCodAcaoInicio');
$obTxtAcaoInicio->setRotulo('Código Ação');
$obTxtAcaoInicio->setTitle('Informe o intervalo de Códigos de Ação a consultar.');
$obTxtAcaoInicio->setInteiro(true);
$obTxtAcaoInicio->setMascara('9999');
$obTxtAcaoInicio->setPreencheComZeros('E');

# Define intervalo final da ação
$obTxtAcaoFim= new TextBox();
$obTxtAcaoFim->setName('inCodAcaoFim');
$obTxtAcaoFim->setRotulo('Código Ação');
$obTxtAcaoFim->setInteiro(true);
$obTxtAcaoFim->setMascara('9999');
$obTxtAcaoFim->setPreencheComZeros('E');

$arTxtIntervaloAcao = array($obTxtAcaoInicio, $obLblIntervalo, $obTxtAcaoFim);

$obTxtTitulo = new TextArea;
$obTxtTitulo->setName   ('stTitulo');
$obTxtTitulo->setId     ('stTitulo');
$obTxtTitulo->setRotulo ('Título');
$obTxtTitulo->setTitle  ('Informe o título da ação.');
$obTxtTitulo->setNull   (true);
$obTxtTitulo->setMaxCaracteres(480);

# Define o formulário e acrescenta todos os componentes
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnExibePPA);

$obITextBoxSelectPPA->geraFormulario($obFormulario);
$obFormulario->addComponente($obIPopUpPrograma);
$obFormulario->agrupaComponentes(array($obRadTipoOrcamentaria,$obRadTipoNaoOrcamentaria));
$obFormulario->addComponente($obSlSubTipoAcao);
if ( ($inCodUf == 2) || ($inCodUf == 27)) 
    $obFormulario->addComponente($obSlIdentificadorAcao);
$obFormulario->agrupaComponentes($arTxtIntervaloAcao);
$obFormulario->addComponente($obTxtTitulo);

$obFormulario->ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
