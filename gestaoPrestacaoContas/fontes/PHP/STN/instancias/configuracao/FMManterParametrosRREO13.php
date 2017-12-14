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
 * Formulario para inclusao dos dados do Relatorio RREO Anexo 13
 *
 * @category    Urbem
 * @package     STN
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include CAM_FW_INCLUDE . 'cabecalho.inc.php';
include CAM_GF_ORC_COMPONENTES . 'ITextBoxSelectEntidadeGeral.class.php';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCManterParametrosRREO13.php';

Sessao::remove('arPeriodo');

//Instancia um objeto Form
$obForm = new Form();
$obForm->setAction('PRManterParametrosRREO13.php');
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden();
$obHdnAcao->setName    ('stAcao');
$obHdnAcao->setValue   ($stAcao );

//Recupera a entidade RPPS
$stFiltro = " WHERE exercicio = '" . Sessao::getExercicio() . "'
                AND cod_modulo = 8
                AND parametro = 'cod_entidade_rpps'";
$inCodEntidadeRPPS = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);

//Instancia o componente ITextBoxSelectEntidadeGeral
$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidadeGeral->inExercicio = Sessao::read      ('exercicio');
$obITextBoxSelectEntidadeGeral->setCodEntidade                  ($inCodEntidadeRPPS);
$obITextBoxSelectEntidadeGeral->setLabel(true);

//Instancia um componente Select para o Ano
$obSlAno = new Select();
$obSlAno->setName    ('stAno');
$obSlAno->setId      ('stAno');
$obSlAno->setRotulo  ('Exercício');
$obSlAno->setTitle   ('Informe o exercício.');
$obSlAno->addOption  ('', 'Selecione');
for ($i = Sessao::read('exercicio') - 1; $i <= Sessao::read('exercicio') + 75; $i++) {
    $obSlAno->addOption($i, $i);
}
$obSlAno->setObrigatorioBarra(true);

//Instancia um objeto Numerico para o valor das receitas previdenciarias
$obTxtReceitaPrevidenciaria = new Numerico      ();
$obTxtReceitaPrevidenciaria->setName            ('flReceitaPrevidenciaria');
$obTxtReceitaPrevidenciaria->setId              ('flReceitaPrevidenciaria');
$obTxtReceitaPrevidenciaria->setRotulo          ('Valor da Receita Previdenciária');
$obTxtReceitaPrevidenciaria->setTitle           ('Informe o valor da receita previdencia');
$obTxtReceitaPrevidenciaria->setMaxLength       (15);
$obTxtReceitaPrevidenciaria->setSize            (18);
$obTxtReceitaPrevidenciaria->setNegativo        (false);
$obTxtReceitaPrevidenciaria->setObrigatorioBarra(true);

//Instancia um objeto Numerico para o valor das despesas previdenciarias
$obTxtDespesaPrevidenciaria = new Numerico      ();
$obTxtDespesaPrevidenciaria->setName            ('flDespesaPrevidenciaria');
$obTxtDespesaPrevidenciaria->setId              ('flDespesaPrevidenciaria');
$obTxtDespesaPrevidenciaria->setRotulo          ('Valor da Despesa Previdenciária');
$obTxtDespesaPrevidenciaria->setTitle           ('Informe o valor da despesa previdencia');
$obTxtDespesaPrevidenciaria->setMaxLength       (15);
$obTxtDespesaPrevidenciaria->setSize            (18);
$obTxtDespesaPrevidenciaria->setNegativo        (false);
$obTxtDespesaPrevidenciaria->setObrigatorioBarra(true);

//Instancia um objeto Numerico para o valor do saldo financeiro
$obTxtSaldoFinanceiro = new Numerico      ();
$obTxtSaldoFinanceiro->setName            ('flSaldoFinanceiro');
$obTxtSaldoFinanceiro->setId              ('flSaldoFinanceiro');
$obTxtSaldoFinanceiro->setRotulo          ('Valor do Saldo Financeiro');
$obTxtSaldoFinanceiro->setTitle           ('Informe o valor do saldo financeiro');
$obTxtSaldoFinanceiro->setMaxLength       (15);
$obTxtSaldoFinanceiro->setSize            (18);
$obTxtSaldoFinanceiro->setNegativo        (false);
$obTxtSaldoFinanceiro->setObrigatorioBarra(true);

//Instancia um span para a lista
$obSpnLista = new Span();
$obSpnLista->setId    ('spnLista');

//Instancia um botao incluir para incluir os dados do formulario na lista
$obBtnIncluir = new Button();
$obBtnIncluir->setValue   ('Incluir');
$obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirValorAtuarial');");

//Instancia um botao para limpar o formulario
$obBtnLimpar = new Button();
$obBtnLimpar->setValue   ('Limpar');
$obBtnLimpar->setId      ('Limpar');
$obBtnLimpar->obEvento->setOnClick ('limpaFormularioAux();');

//Instancia um Ok
$obOk = new Ok();

//Instancia um Limpar
$obLimpar = new Limpar         ();
$obLimpar->obEvento->setOnClick("LimparForm();");

//Instancia um objeto Formulario
$obFormulario = new Formulario       ();
$obFormulario->addForm               ($obForm   );
$obFormulario->addHidden             ($obHdnAcao);

$obFormulario->addTitulo             ('Entidade');
$obFormulario->addComponente         ($obITextBoxSelectEntidadeGeral);
$obFormulario->addTitulo             ('Valores do Período');
$obFormulario->addComponente         ($obSlAno);
$obFormulario->addComponente         ($obTxtReceitaPrevidenciaria);
$obFormulario->addComponente         ($obTxtDespesaPrevidenciaria);
$obFormulario->addComponente         ($obTxtSaldoFinanceiro);
$obFormulario->defineBarra           (array($obBtnIncluir, $obBtnLimpar));
$obFormulario->addSpan               ($obSpnLista);

$obFormulario->defineBarra           (array($obOk,$obLimpar));
$obFormulario->show                  ();

$jsOnload = "montaParametrosGET('buscaValoresAtuariais');";

include 'JSManterParametrosRREO13.js';
include CAM_FW_INCLUDE . 'rodape.inc.php';
?>
