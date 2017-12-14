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
    * Página de formulário da Elaboração de Estimativa da Receita
    * Data de Criação: 07/04/2009

    * @author Analista: Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package      URBEM
    * @subpackage   PPA

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';
require_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ElaborarEstimativaReceita';
$pgOcul     = 'OC'.$stPrograma.".php";
$pgProc     = 'PR'.$stPrograma.".php";
$pgForm     = 'FM'.$stPrograma.".php";
$pgJs       = 'JS'.$stPrograma.".js";

require $pgJs;

$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($stCtrl);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//Recupera os ppas para o select
$obTPPA = new TPPA;
$obTPPA->recuperaTodos($rsPPA, ' ORDER BY ano_inicio ');

//Instancia um textboxSelect para a PPA
$obTextBoxSelectPPA = new ITextBoxSelectPPA();
$obTextBoxSelectPPA->setNull(false);
$obTextBoxSelectPPA->setPreencheUnico(true);
$obTextBoxSelectPPA->obTextBox->obEvento->setOnChange("montaParametrosGET('montaListagemReceitas', 'inCodPPA');");
$obTextBoxSelectPPA->obSelect->obEvento->setOnChange ("montaParametrosGET('montaListagemReceitas','inCodPPA');");
//$obTextBoxSelectPPA = new TextBoxSelect;
//$obTextBoxSelectPPA->setRotulo                       ('PPA');
//$obTextBoxSelectPPA->setTitle                        ('Informe a PPA.');
//$obTextBoxSelectPPA->setName                         ('inCodPPA');
//$obTextBoxSelectPPA->obTextBox->setName              ('inCodPPATxt');
//$obTextBoxSelectPPA->obTextBox->setId                ('inCodPPATxt');
//$obTextBoxSelectPPA->obSelect->setName               ('inCodPPA');
//$obTextBoxSelectPPA->obSelect->setId                 ('inCodPPA');
//$obTextBoxSelectPPA->obSelect->addOption             ('','Selecione');
//$obTextBoxSelectPPA->obSelect->setDependente         (true);
//$obTextBoxSelectPPA->obSelect->setCampoID            ('cod_ppa');
//$obTextBoxSelectPPA->obSelect->setCampoDesc          ('[ano_inicio] - [ano_final]');
//$obTextBoxSelectPPA->obSelect->preencheCombo         ($rsPPA);
//$obTextBoxSelectPPA->setNull                         (false);

// Define radio de porcentagem analitica
$obRadPorcentagemAnalitica = new Radio();
$obRadPorcentagemAnalitica->setId('stTipoPercentualInformado_A');
$obRadPorcentagemAnalitica->setName('stTipoPercentualInformado');
$obRadPorcentagemAnalitica->setRotulo('Porcentagem');
$obRadPorcentagemAnalitica->setLabel('Analítica');
$obRadPorcentagemAnalitica->setValue('A');
$obRadPorcentagemAnalitica->setNull(false);
$obRadPorcentagemAnalitica->setChecked(true);
$obRadPorcentagemAnalitica->obEvento->setOnChange("montaParametrosGET('montaPorcentagemAnalitica');");
$obRadPorcentagemAnalitica->setDisabled('true');

// Define radio de porcentagem sintetica
$obRadPorcentagemSintetica = new Radio();
$obRadPorcentagemSintetica->setId('stTipoPercentualInformado_S');
$obRadPorcentagemSintetica->setName('stTipoPercentualInformado');
$obRadPorcentagemSintetica->setRotulo('Porcentagem');
$obRadPorcentagemSintetica->setLabel('Sintética');
$obRadPorcentagemSintetica->setValue('S');
$obRadPorcentagemSintetica->setNull(false);
$obRadPorcentagemSintetica->obEvento->setOnChange("montaParametrosGET('montaPorcentagemSintetica');");
$obRadPorcentagemSintetica->setDisabled('true');

$arRadPorcentagem = array($obRadPorcentagemAnalitica, $obRadPorcentagemSintetica);

// Define Span para as radios de porcentagem.
$obSpnPorcentagem = new Span;
$obSpnPorcentagem->setID('spnPorcentagem');

$obSpnListagemReceitas = new Span;
$obSpnListagemReceitas->setID('spnListagemReceitas');

$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnCtrl);
$obFormulario->addTitulo        ('Elaboração da Estimativa da Receita');
$obFormulario->addComponente    ($obTextBoxSelectPPA);
$obFormulario->agrupaComponentes($arRadPorcentagem);
$obFormulario->addSpan          ($obSpnPorcentagem);
$obFormulario->addSpan          ($obSpnListagemReceitas);

$obFormulario->Ok();
$obFormulario->show();

$rsPPA = $obTextBoxSelectPPA->getRecordSet();
if ($rsPPA->getNumLinhas() == 1) {
    $jsOnload = "montaParametrosGET('montaListagemReceitas', 'inCodPPA');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
