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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 28/04/2005

    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Id: FLAnexo13.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

include_once 'JSAnexo13.js';

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obREntidade->listarUsuariosEntidade($rsEntidades , ' ORDER BY cod_entidade');
$rsRecordset = new RecordSet;

$obForm = new Form;
$obForm->setAction('OCGeraAnexo13.php');
$obForm->setTarget('telaPrincipal');

//Combo de Demonstração de Despesa
$obCmbTipoRelatorio= new Select;
$obCmbTipoRelatorio->setRotulo('Demonstrar Despesa');
$obCmbTipoRelatorio->setName  ('stDemonstrarDespesa');
$obCmbTipoRelatorio->setStyle ('width: 200px'  );
$obCmbTipoRelatorio->addOption('', 'Selecione' );
$obCmbTipoRelatorio->addOption('E', 'Empenhada');
$obCmbTipoRelatorio->addOption('L', 'Liquidada');
$obCmbTipoRelatorio->addOption('P', 'Paga'     );
$obCmbTipoRelatorio->setNull  (false );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName  ('inCodEntidade');
$obCmbEntidades->setRotulo('Entidades');
$obCmbEntidades->setTitle ('');
$obCmbEntidades->setNull  (false);

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1  ('cod_entidade');
$obCmbEntidades->setCampoDesc1('nom_cgm');
$obCmbEntidades->SetRecord1   ($rsEntidades);

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2('inCodEntidade');
$obCmbEntidades->setCampoId2  ('cod_entidade');
$obCmbEntidades->setCampoDesc2('nom_cgm');
$obCmbEntidades->SetRecord2   ($rsRecordset);

// define objeto Data
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (Sessao::getExercicio());
$obPeriodo->setNull           (false);
$obPeriodo->setValidaExercicio(true);
$obPeriodo->setValue          (4);

//Combo de Demonstração de Despesa
$obCmbTipoEmissao= new Select;
$obCmbTipoEmissao->setRotulo('Tipo de Relatório');
$obCmbTipoEmissao->setName  ('inCodTipoRelatorio');
$obCmbTipoEmissao->setStyle ('width: 200px'      );
$obCmbTipoEmissao->addOption('', 'Selecione'     );
$obCmbTipoEmissao->addOption('1', 'Por Função'   );
$obCmbTipoEmissao->addOption('2', 'Por Categoria Econômica');
$obCmbTipoEmissao->setNull  (false);

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm );
$obFormulario->addTitulo    ('Dados para Filtro');
$obFormulario->addComponente($obCmbEntidades     );
$obFormulario->addComponente($obPeriodo );
$obFormulario->addComponente($obCmbTipoRelatorio );
$obFormulario->addComponente($obCmbTipoEmissao   );
$obFormulario->OK();
$obFormulario->show();
?>
