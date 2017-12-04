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
    * Página de Formulario que filtra de Relatórios de Regiões
    * Data de Criação: 21/10/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.08
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(CAM_GF_PPA_COMPONENTES."ITextBoxSelectPPA.class.php");
include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectTipoPrograma.class.php';
require_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "Programa";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction('OCGeraRelatorio'.$stPrograma.'.php');
$obForm->setTarget('telaPrincipal');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($_REQUEST['stAcao']);

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue(CAM_GF_PPA_INSTANCIAS."relatorio/".$pgOcul);

### ITextBoxSelectPPA ###
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
//$obITextBoxSelectPPA->setCodPPA($inCodPPA);
$obITextBoxSelectPPA->setNull(false);
$obITextBoxSelectPPA->setPreencheUnico(true);

### Informar Código Programa Inicial ###
$obTextBoxProgramaIni = new Inteiro;
$obTextBoxProgramaIni->setRotulo('Programas');
$obTextBoxProgramaIni->setName('inNumProgramaIni');
$obTextBoxProgramaIni->setId('inNumProgramaIni');
$obTextBoxProgramaIni->setNull(true);
$obTextBoxProgramaIni->setMaxLength(4);
$obTextBoxProgramaIni->setMascara('9999');
$obTextBoxProgramaIni->setPreencheComZeros('E');
$obTextBoxProgramaIni->setTitle('Escolha um codigo para Programa');
$obTextBoxProgramaIni->setSize(8);

### Label Programa ###
$obLabelPrograma = new Label();
$obLabelPrograma->setValue('à');

### Informar Código Programa Final ###
$obTextBoxProgramaFim = new Inteiro;
$obTextBoxProgramaFim->setRotulo('Programas');
$obTextBoxProgramaFim->setName('inNumProgramaFim');
$obTextBoxProgramaFim->setId('inNumProgramaFim');
$obTextBoxProgramaFim->setNull(true);
$obTextBoxProgramaFim->setMascara('9999');
$obTextBoxProgramaFim->setPreencheComZeros('E');
$obTextBoxProgramaFim->setMaxLength(4);
$obTextBoxProgramaFim->setTitle('Escolha um codigo para programa');
$obTextBoxProgramaFim->setSize(8);

$obITextBoxSelectTipoPrograma = new ITextBoxSelectTipoPrograma();
$obITextBoxSelectTipoPrograma->setNull(true);

### Natureza (Continuo) ###
$obRadioNaturezaContinuos = new Radio();
$obRadioNaturezaContinuos->setName("stNatureza");
$obRadioNaturezaContinuos->setId("stNaturezaContinuo");
$obRadioNaturezaContinuos->setRotulo('Natureza Temporal');
$obRadioNaturezaContinuos->setLabel("Contínuos");
$obRadioNaturezaContinuos->setValue("1");

### Natureza (Temporários) ###
$obRadioNaturezaTemporarios = new Radio();
$obRadioNaturezaTemporarios->setName("stNatureza");
$obRadioNaturezaTemporarios->setId("stNaturezaTemporario");
$obRadioNaturezaTemporarios->setLabel("Temporários");
$obRadioNaturezaTemporarios->setValue("2");

### Natureza (Todos) ###
$obRadioNaturezaTodos = new Radio();
$obRadioNaturezaTodos->setName("stNatureza");
$obRadioNaturezaTodos->setId("stNaturezaTodos");
$obRadioNaturezaTodos->setLabel("Todos");
$obRadioNaturezaTodos->setValue("");
$obRadioNaturezaTodos->setChecked(true);

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCaminho);
$obFormulario->addTitulo('Filtro Programas');
$obFormulario->addComponente($obITextBoxSelectPPA);
$obFormulario->agrupaComponentes(array($obTextBoxProgramaIni, $obLabelPrograma, $obTextBoxProgramaFim));
$obFormulario->addComponente($obITextBoxSelectTipoPrograma);
$obFormulario->agrupaComponentes(array($obRadioNaturezaContinuos, $obRadioNaturezaTemporarios, $obRadioNaturezaTodos));

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName('Limpar');
$obBtnLimpar->setValue('Limpar');
$stOnClick = "limpaFormulario();
              jq('#inNumProgramaIni').val('');
              jq('#inNumProgramaFim').val('');
              jq('#stNaturezaTodos').attr('checked','checked');";
$obBtnLimpar->obEvento->setOnClick($stOnClick);

$obFormulario->defineBarra(array($obBtnOk , $obBtnLimpar));

//$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
