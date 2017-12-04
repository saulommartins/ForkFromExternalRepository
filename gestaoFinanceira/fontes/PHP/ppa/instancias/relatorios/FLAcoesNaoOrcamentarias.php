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
    * Página de Formulario que filtra de Relatórios de Ações Não Orçamentárias
    * Data de Criação: 22/05/2008

    * @author Analista: Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package URBEM
    * @subpackage PPA

    * $Id: $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';
require_once CAM_GF_PPA_MAPEAMENTO.'TPPATipoAcao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "AcoesNaoOrcamentarias";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

// Bloqueia o campo do cod_programa e esconde o botao de popUp do buscaInner de programa para obrigar o usuario a escolher uma PPA antes.
$jsOnLoad  = 'jq("#btAbrePopUp").css("display", "none"); jq("#inCodPrograma").attr("readOnly", true);';

$obForm = new Form;
$obForm->setAction('OCGeraRelatorio'.$stPrograma.'.php');
$obForm->setTarget('telaPrincipal');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($_REQUEST['stAcao']);

//Recupera os ppas para o select
$obTPPA = new TPPA;
$obTPPA->recuperaTodos($rsPPA, ' ORDER BY ano_inicio ');

// Realiza a verificação se o valor for diferente de vazio para liberar o botão de pesquisa dos dados do programa
// para assim poder realizar os dados com os dados filtrados pelo cod_ppa que deve ser selecionado
$jsChange = "
    if (this.value != '') {
        jq('#btAbrePopUp').css('display', 'inline');
        jq('#inCodPrograma').attr('readOnly', false);
    } else {
        jq('#btAbrePopUp').css('display', 'none');
        jq('#inCodPrograma').attr('readOnly', true).val('');
        jq('#stNomPrograma').html('&nbsp;')
    }
";

//Instancia um textboxSelect para a PPA
$obTextBoxSelectPPA = new TextBoxSelect;
$obTextBoxSelectPPA->setRotulo('PPA');
$obTextBoxSelectPPA->setTitle('Informe o PPA.');
$obTextBoxSelectPPA->setName('inCodPPA');
$obTextBoxSelectPPA->obTextBox->setName('inCodPPATxt');
$obTextBoxSelectPPA->obTextBox->setId('inCodPPATxt');
$obTextBoxSelectPPA->obTextBox->obEvento->setOnChange($jsChange);
$obTextBoxSelectPPA->obSelect->setName('inCodPPA');
$obTextBoxSelectPPA->obSelect->setId('inCodPPA');
$obTextBoxSelectPPA->obSelect->addOption('','Selecione');
$obTextBoxSelectPPA->obSelect->setDependente(true);
$obTextBoxSelectPPA->obSelect->setCampoID('cod_ppa');
$obTextBoxSelectPPA->obSelect->setCampoDesc('[ano_inicio] - [ano_final]');
$obTextBoxSelectPPA->obSelect->preencheCombo($rsPPA);
$obTextBoxSelectPPA->obSelect->obEvento->setOnChange($jsChange);
$obTextBoxSelectPPA->setNull(false);
if ($rsPPA->getNumLinhas() == 1) {
    $obTextBoxSelectPPA->obTextBox->setValue($rsPPA->getCampo('cod_ppa'));
    $obTextBoxSelectPPA->obSelect->setValue($rsPPA->getCampo('cod_ppa'));
}

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
$obIPopUpPrograma->obImagem->setId('btAbrePopUp');
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

$obTPPATipoAcao = new TPPATipoAcao;
$stWhere =  ' WHERE cod_tipo > 3';
$obTPPATipoAcao->recuperaTodos($rsTipoAcao, $stWhere, 'cod_tipo');

$obSelectPPATipoAcao = new Select;
$obSelectPPATipoAcao->setRotulo('Tipo de Ação');
$obSelectPPATipoAcao->setTitle('Informe o Tipo de Ação.');
$obSelectPPATipoAcao->setName('inCodTipoAcao');
$obSelectPPATipoAcao->setId ('inCodTipoAcao');
$obSelectPPATipoAcao->addOption(0, 'Todos os Tipos');
$obSelectPPATipoAcao->setCampoID('cod_tipo');
$obSelectPPATipoAcao->setCampoDesc('descricao');
$obSelectPPATipoAcao->preencheCombo($rsTipoAcao);

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addComponente($obTextBoxSelectPPA);
$obFormulario->addComponente($obIPopUpPrograma);
$obFormulario->addComponente($obSelectPPATipoAcao);
$obFormulario->Ok();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
