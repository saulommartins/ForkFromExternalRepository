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
* Filtro de Configuração de Ratificador TCM-BA
* Data de Criação: 11/08/2015

* @author Analista: Ane Caroline Fiegenbaum Pereira
* @author Desenvolvedor: Jean Silva 

$Id: FLManterConfiguracaoRatificador.php 63383 2015-08-24 12:34:24Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadeLancamentoValor.class.php';
include_once CAM_GF_PPA_COMPONENTES.'MontaOrgaoUnidade.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoRatificador";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao   = $request->get('stAcao');

$obRegra = new RContabilidadeLancamentoValor;
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade, "E.numcgm" );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgForm );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ( "inCodEntidade"                 );
$obHdnEntidade->setValue( $request->get('inCodEntidade')  );

$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "modulo"                 );
$obHdnModulo->setValue( $request->get('modulo')  );

$obISelectEntidade = new ITextBoxSelectEntidadeGeral();
$obISelectEntidade->setCodEntidade(1);

// Define unidade orçamentária responsável
$obIMontaUnidadeOrcamentaria = new MontaOrgaoUnidade();
$obIMontaUnidadeOrcamentaria->setRotulo             ('Unidade Orçamentária'  );
$obIMontaUnidadeOrcamentaria->setValue              ( $stUnidadeOrcamentaria );
$obIMontaUnidadeOrcamentaria->setCodOrgao           ('');
$obIMontaUnidadeOrcamentaria->setCodUnidade         ('');
$obIMontaUnidadeOrcamentaria->setActionPosterior    ($pgForm);
$obIMontaUnidadeOrcamentaria->setNull               (false);
$obIMontaUnidadeOrcamentaria->setTitle              ("Código do Orgão/Unidade.");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo     ( "Configuração de Ratificador"  );
$obFormulario->addHidden     ( $obHdnAcao     );
$obFormulario->addHidden     ( $obHdnModulo   );
$obFormulario->addHidden     ( $obHdnCtrl   );
$obFormulario->addComponente ( $obISelectEntidade );
$obIMontaUnidadeOrcamentaria->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
