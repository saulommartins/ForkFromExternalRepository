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
 * Formulario de Inclusao de Cheques
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GT_MON_COMPONENTES . 'IMontaAgenciaConta.class.php';
include CAM_GF_TES_CONTROLE . 'CTesourariaCheque.class.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaCheque.class.php';

$stAcao = $request->get('stAcao');

//Instancia o model e o controller
$obModel      = new RTesourariaCheque();
$obController = new CTesourariaCheque($obModel);

$obController->getCheque($rsCheque, $_REQUEST);

//Instancia label para o banco
$obLblBanco = new Label();
$obLblBanco->setRotulo('Banco');
$obLblBanco->setValue($rsCheque->getCampo('num_banco') . ' - ' . $rsCheque->getCampo('nom_banco'));

//Instancia label para o agencia
$obLblAgencia = new Label();
$obLblAgencia->setRotulo('Agência');
$obLblAgencia->setValue($rsCheque->getCampo('num_agencia') . ' - ' . $rsCheque->getCampo('nom_agencia'));

//Instancia label para o conta corrente
$obLblContaCorrente = new Label();
$obLblContaCorrente->setRotulo('Conta Corrente');
$obLblContaCorrente->setValue($rsCheque->getCampo('num_conta_corrente'));

//Instancia label para o numero do cheque
$obLblCheque = new Label();
$obLblCheque->setRotulo('Número do Cheque');
$obLblCheque->setValue($rsCheque->getCampo('num_cheque'));

//Instancia label para data de emissao
$obLblDataEmissao = new Label();
$obLblDataEmissao->setRotulo('Data de Emissão');
$obLblDataEmissao->setValue($rsCheque->getCampo('data_emissao'));

//Instancia label para o valor
$obLblValor = new Label();
$obLblValor->setRotulo('Valor');
$obLblValor->setValue(number_format($rsCheque->getCampo('valor'),2,',','.'));

//Instancia label para a descricao
$obLblDescricao = new Label();
$obLblDescricao->setRotulo('Descrição');
$obLblDescricao->setValue($rsCheque->getCampo('descricao'));

//Instancia label para o exercicio
$obLblExercicio = new Label();
$obLblExercicio->setRotulo('Exercício');
$obLblExercicio->setValue($rsCheque->getCampo('exercicio'));

//Instancia label para a entidade
$obLblEntidade = new Label();
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue($rsCheque->getCampo('cod_entidade') . ' - ' . $rsCheque->getCampo('nom_entidade'));

switch ($rsCheque->getCampo('tipo_emissao')) {
case 'ordem_pagamento':
    //Instancia label para o numero o numero da op
    $obLblCodOrdem = new Label();
    $obLblCodOrdem->setRotulo('Ordem de Pagamento');
    $obLblCodOrdem->setValue($rsCheque->getCampo('cod_ordem'));

    break;
case 'despesa_extra':
    //Instancia um label para o numero do recibo extra
    $obLblReciboExtra = new Label();
    $obLblReciboExtra->setRotulo('Nr. Recibo Extra');
    $obLblReciboExtra->setValue ($rsCheque->getCampo('cod_recibo_extra'));

    break;
case 'transferencia':
    //Instancia label para o numero a conta credito
    $obLblContaCredito = new Label();
    $obLblContaCredito->setRotulo('Conta Crédito');
    $obLblContaCredito->setValue($rsCheque->getCampo('cod_plano_credito') . ' - ' . $rsCheque->getCampo('nom_plano_credito'));

    //Instancia label para o numero a conta debito
    $obLblContaDebito = new Label();
    $obLblContaDebito->setRotulo('Conta Débito');
    $obLblContaDebito->setValue($rsCheque->getCampo('cod_plano_debito') . ' - ' . $rsCheque->getCampo('nom_plano_debito'));

    break;
}

if ($rsCheque->getCampo('data_anulacao')) {
    $obLblDataAnulacao = new Label();
    $obLblDataAnulacao->setRotulo('Data de Anulação');
    $obLblDataAnulacao->setValue($rsCheque->getCampo('data_anulacao'));
}

if ($rsCheque->getCampo('tipo_emissao') != '') {
    $obLblChequeBaixado = new Label();
    $obLblChequeBaixado->setRotulo ('Baixado'                            );
    $obLblChequeBaixado->setValue  ($rsCheque->getCampo('cheque_baixado'));

    //Instancia label para a data de entrada
    $obLblDataBaixa = new Label();
    $obLblDataBaixa->setRotulo('Data de Baixa');
    $obLblDataBaixa->setValue($rsCheque->getCampo('data_baixa'));
}

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('PRManterCheque.php');
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Botao de voltar
$obBtVoltar = new Button();
$obBtVoltar->setName ('btVoltar');
$obBtVoltar->setId   ('btVoltar');
$obBtVoltar->setValue('Voltar'  );
$obBtVoltar->obEvento->setOnClick("Cancelar('LSManterCheque.php?stAcao=consultar','telaPrincipal');");

//Instancia um objeto Formulario
$obFormulario = new Formulario();
$obFormulario->addForm        ($obForm            );
$obFormulario->addHidden      ($obHdnAcao         );

$obFormulario->addTitulo      ('Dados do Cheque'  );

$obFormulario->addComponente  ($obLblBanco        );
$obFormulario->addComponente  ($obLblAgencia      );
$obFormulario->addComponente  ($obLblContaCorrente);
$obFormulario->addComponente  ($obLblCheque       );

if ($rsCheque->getCampo('tipo_emissao') != '') {
    $obFormulario->addTitulo      ('Dados da Emissão' );

    $obFormulario->addComponente  ($obLblDataEmissao  );
    $obFormulario->addComponente  ($obLblValor        );
    $obFormulario->addComponente  ($obLblDescricao    );
}

switch ($rsCheque->getCampo('tipo_emissao')) {
case 'ordem_pagamento':
    $obFormulario->addTitulo    ('Dados da Ordem de Pagamento');
    $obFormulario->addComponente($obLblExercicio);
    $obFormulario->addComponente($obLblCodOrdem );
    $obFormulario->addComponente($obLblEntidade );

    break;
case 'despesa_extra':
    $obFormulario->addTitulo    ('Dados da Despesa Extra');
    $obFormulario->addComponente($obLblExercicio);
    $obFormulario->addComponente($obLblReciboExtra);
    $obFormulario->addComponente($obLblEntidade );

    break;
case 'transferencia':
    $obFormulario->addTitulo    ('Dados da Transferência');
    $obFormulario->addComponente($obLblExercicio   );
    $obFormulario->addComponente($obLblEntidade    );
    $obFormulario->addComponente($obLblContaCredito);
    $obFormulario->addComponente($obLblContaDebito );

    break;
}
if ($rsCheque->getCampo('data_anulacao') != '') {
    $obFormulario->addTitulo    ('Dados da Anulação');
    $obFormulario->addComponente($obLblDataAnulacao );
}
if ($rsCheque->getCampo('tipo_emissao') != '') {
    $obFormulario->addComponente    ($obLblChequeBaixado);
    $obFormulario->addComponente    ($obLblDataBaixa    );
}

$obFormulario->defineBarra      (array($obBtVoltar));

$obFormulario->show           ();

?>
