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

    $Id: FLManterTransferirBem.php 36840 2009-01-06 21:16:27Z luiz $

    * Casos de uso: uc-03.01.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";
include_once CAM_GP_PAT_COMPONENTES."ISelectEspecie.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";

$stPrograma = "CargaPatrimonial";
$pgPR     = "PR".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgPR);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

$obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidade->obTextBox->setNull(false);
$obISelectEntidade->setNull(false);

//instancia o componenete Natureza/Grupo/Espécie
$obISelectEspecie = new ISelectEspecie($obForm);
$obISelectEspecie->obSelectEspecie->setNull(true);
$obISelectEspecie->obISelectGrupo->obSelectGrupo->setNull(true);

//instancia o componenete IMontaOrganograma
$obIMontaOrganograma = new IMontaOrganograma(true);
$obIMontaOrganograma->setCodOrgao($codOrgao);
$obIMontaOrganograma->setCadastroOrganograma(true);
$obIMontaOrganograma->setNivelObrigatorio(1);

$obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
$obIMontaOrganogramaLocal->setValue($codLocal);

$obRadioTipoRelatorioCompleto = new Radio;
$obRadioTipoRelatorioCompleto->setName('tipoRelatorio');
$obRadioTipoRelatorioCompleto->setChecked(true);
$obRadioTipoRelatorioCompleto->setLabel('Completo com Totalizador');
$obRadioTipoRelatorioCompleto->setRotulo("Tipo de Relatório");
$obRadioTipoRelatorioCompleto->setValue(0);

$obRadioTipoRelatorioCompletoTotalizador = new Radio;
$obRadioTipoRelatorioCompletoTotalizador->setName('tipoRelatorio');
$obRadioTipoRelatorioCompletoTotalizador->setChecked(false);
$obRadioTipoRelatorioCompletoTotalizador->setLabel('Somente Totalizador');
$obRadioTipoRelatorioCompletoTotalizador->setRotulo("Tipo de Relatório");
$obRadioTipoRelatorioCompletoTotalizador->setValue(2);

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.01.06');
$obFormulario->addForm      ( $obForm );

$obFormulario->addTitulo( 'Filtros para Relatório de Carga Patrimonial' );
$obFormulario->addComponente    ( $obISelectEntidade               );
$obISelectEspecie->geraFormulario( $obFormulario );
$obIMontaOrganograma->geraFormulario( $obFormulario );
$obIMontaOrganogramaLocal->geraFormulario( $obFormulario );

$obFormulario->agrupaComponentes(array($obRadioTipoRelatorioCompleto,$obRadioTipoRelatorioCompletoTotalizador));

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
