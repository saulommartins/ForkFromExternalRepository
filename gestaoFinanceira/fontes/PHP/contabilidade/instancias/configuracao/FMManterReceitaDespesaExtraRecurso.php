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
  * Página de Formulario de Configuração de Receita/Despesa Extra por Fonte de Recurso
  * Data de Criação: 05/11/2015

  * @author Analista: Valtair Santos
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: FMManterReceitaDespesaExtraRecurso.php 63906 2015-11-05 12:31:01Z franver $
  * $Revision: 63906 $
  * $Author: franver $
  * $Date: 2015-11-05 10:31:01 -0200 (Thu, 05 Nov 2015) $
*/
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php";
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterReceitaDespesaExtraRecurso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

//$stCtrl = $request->get("stCtrl");
$stAcao = $request->get("stAcao");

$boIndicadorContasExtrasRecursos = SistemaLegado::pegaConfiguracao('indicador_contas_extras_recurso',9,Sessao::getExercicio(),$boTransacao);

require_once($pgJs);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setId   ("stAcao");
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl2");
$obHdnCtrl->setId   ("stCtrl2");
$obHdnCtrl->setValue($stCtrl);

$obRdSaldoContasRecursoSim = new Radio();
$obRdSaldoContasRecursoSim->setRotulo           ("Permitir Saldo de Contas por Recurso.");
$obRdSaldoContasRecursoSim->setTitle            ("Selecione caso tenha a opção de preencher Saldos de Contas por Recurso.");
$obRdSaldoContasRecursoSim->setLabel            ("Sim");
$obRdSaldoContasRecursoSim->setName             ("boIndicadorSaldoContasRecurso");
$obRdSaldoContasRecursoSim->setId               ("boIndicadorSaldoContasRecurso");
if($boIndicadorContasExtrasRecursos == 't'){
    $obRdSaldoContasRecursoSim->setChecked          (true);
}
$obRdSaldoContasRecursoSim->setValue            ('t');
$obRdSaldoContasRecursoSim->setNull             ( false );
$obRdSaldoContasRecursoSim->obEvento->setOnChange("montaParametrosGET('montaContas');");

$obRdSaldoContasRecursoNao = new Radio();
$obRdSaldoContasRecursoNao->setRotulo           ("Permitir Saldo de Contas por Recurso.");
$obRdSaldoContasRecursoNao->setTitle            ("Selecione caso tenha a opção de preencher Saldos de Contas por Recurso.");
$obRdSaldoContasRecursoNao->setLabel            ("Não");
$obRdSaldoContasRecursoNao->setName             ("boIndicadorSaldoContasRecurso");
$obRdSaldoContasRecursoNao->setId               ("boIndicadorSaldoContasRecurso");
if($boIndicadorContasExtrasRecursos == 'f'){
    $obRdSaldoContasRecursoNao->setChecked          (true);
}
$obRdSaldoContasRecursoNao->setValue            ('f');
$obRdSaldoContasRecursoNao->setNull             ( false );
$obRdSaldoContasRecursoNao->obEvento->setOnChange("montaParametrosGET('montaContas');");

$obSpnContaReceitaDespesaExtra = new Span();
$obSpnContaReceitaDespesaExtra->setId("spnContaReceitaDespesaExtra");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addTitulo     ( "Dados para Configuração de Receita/Despesa Extra por Fonte de Recurso" );
$obFormulario->addComponenteComposto ( $obRdSaldoContasRecursoSim, $obRdSaldoContasRecursoNao );
$obFormulario->addSpan       ( $obSpnContaReceitaDespesaExtra );
$obFormulario->OK(true);
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>