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
 * Formulario configuracao da evolucao do patrimonio liquido
 *
 * @category    Urbem
 * @package     LDO
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

include_once CAM_GF_LDO_NEGOCIO . 'RLDOEvolucaoPatrimonioLiquido.class.php';
include_once CAM_GF_LDO_VISAO   . 'VLDOEvolucaoPatrimonioLiquido.class.php';

include_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';

$stAcao = $request->get('stAcao');

sistemaLegado::BloqueiaFrames(true,false);

ob_flush();

$pgOcul = 'OCEvolucaoPatrimonioLiquido.php';

include_once 'JSEvolucaoPatrimonioLiquido.js';

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('PREvolucaoPatrimonioLiquido.php');
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Instancia um objeto hidden para o cod_ppa
$obHdnCodPPA = new Hidden;
$obHdnCodPPA->setName ('inCodPPA');
$obHdnCodPPA->setValue($_REQUEST['inCodPPA']);

//Instancia um objeto hidden para o ano
$obHdnAno = new Hidden;
$obHdnAno->setName ('inAno');
$obHdnAno->setValue($_REQUEST['slExercicioLDO']);

//Instancia um span para as entidades normais
$obSpnTable = new Span();
$obSpnTable->setId('spnTable');

//Instancia um span para entidades rpps
$obSpnTableRPPS = new Span();
$obSpnTableRPPS->setId('spnTableRPPS');

//recupera os dados das entidades nao rrps para a lista
$obVLDOTipoEvolucaoPatrimonioLiquido = new VLDOEvolucaoPatrimonioLiquido(new RLDOEvolucaoPatrimonioLiquido());
$obVLDOTipoEvolucaoPatrimonioLiquido->listEntidadeNaoRPPS($rsNaoRPPS, $_REQUEST);

//adiciona o registro para o totalizador
$rsNaoRPPS->add(array('cod_tipo'       => '99',
                      'nivel'          => '0',
                      'descricao'      => 'TOTAL',
                      'rpps'           => '0',
                      'valor_1'        => '0',
                      'valor_2'        => '0',
                      'valor_3'        => '0',
                      'porcentagem_1'  => '0.00',
                      'porcentagem_2'  => '0.00',
                      'porcentagem_3'  => '0.00',
                      'orcamento_1'    => '0',
                      'orcamento_2'    => '0',
                      'orcamento_3'    => '0'));

//adiciona a formatacao de moeda
$rsNaoRPPS->addFormatacao('valor_1'      ,'NUMERIC_BR');
$rsNaoRPPS->addFormatacao('valor_2'      ,'NUMERIC_BR');
$rsNaoRPPS->addFormatacao('valor_3'      ,'NUMERIC_BR');
$rsNaoRPPS->addFormatacao('porcentagem_1','NUMERIC_BR');
$rsNaoRPPS->addFormatacao('porcentagem_2','NUMERIC_BR');
$rsNaoRPPS->addFormatacao('porcentagem_3','NUMERIC_BR');

//cria um numerico para os 4 exercicios anteriores ao ldo
$obValorAno1 = new Numerico;
$obValorAno1->setName            ('flValorAno1_[cod_tipo]_[rpps]_[nivel]_[orcamento_1]');
$obValorAno1->setLabel           (true);
$obValorAno1->setClass           ('valor');
$obValorAno1->setValue           ('[valor_1]');
$obValorAno1->setMaxLength       (11);
$obValorAno1->setSize            (14);
$obValorAno1->obEvento->setOnBlur("calculaValor(this.id,'tableNormal');");

$obValorAno2 = new Numerico;
$obValorAno2->setName            ('flValorAno2_[cod_tipo]_[rpps]_[nivel]_[orcamento_2]');
$obValorAno2->setLabel           (true);
$obValorAno2->setClass           ('valor');
$obValorAno2->setValue           ('[valor_2]');
$obValorAno2->setMaxLength       (11);
$obValorAno2->setSize            (14);
$obValorAno2->obEvento->setOnBlur("calculaValor(this.id,'tableNormal');");

$obValorAno3 = new Numerico;
$obValorAno3->setName            ('flValorAno3_[cod_tipo]_[rpps]_[nivel]_[orcamento_3]');
$obValorAno3->setLabel           (true);
$obValorAno3->setClass           ('valor');
$obValorAno3->setValue           ('[valor_3]');
$obValorAno3->setMaxLength       (11);
$obValorAno3->setSize            (14);
$obValorAno3->obEvento->setOnBlur("calculaValor(this.id,'tableNormal');");

$obPorcAno1 = new Numerico;
$obPorcAno1->setName            ('flPorcAno1_[cod_tipo]_[rpps]_[nivel]_[orcamento_1]');
$obPorcAno1->setLabel           (true);
$obPorcAno1->setClass           ('valor');
$obPorcAno1->setValue           ('[porcentagem_1]');

$obPorcAno2 = new Numerico;
$obPorcAno2->setName            ('flPorcAno2_[cod_tipo]_[rpps]_[nivel]_[orcamento_2]');
$obPorcAno2->setLabel           (true);
$obPorcAno2->setClass           ('valor');
$obPorcAno2->setValue           ('[porcentagem_2]');

$obPorcAno3 = new Numerico;
$obPorcAno3->setName            ('flPorcAno3_[cod_tipo]_[rpps]_[nivel]_[orcamento_3]');
$obPorcAno3->setLabel           (true);
$obPorcAno3->setClass           ('valor');
$obPorcAno3->setValue           ('[porcentagem_3]');

//cria a tabela para as entidades nao rpps
$obTable = new Table;
$obTable->setId         ('tableNormal');
$obTable->setRecordset  ($rsNaoRPPS);
//$obTable->setConditional(true, "#efefef");

$obTable->Head->addCabecalho('Descrição', 25);
$obTable->Head->addCabecalho(($_REQUEST['slExercicioLDO'] - 2) ,12);
$obTable->Head->addCabecalho('%',12);
$obTable->Head->addCabecalho(($_REQUEST['slExercicioLDO'] - 3), 12);
$obTable->Head->addCabecalho('% ',12);
$obTable->Head->addCabecalho(($_REQUEST['slExercicioLDO'] - 4), 12);
$obTable->Head->addCabecalho('%  ',12);

$obTable->Body->addCampo('[descricao]', 'E');
$obTable->Body->addCampo($obValorAno1, 'D');
$obTable->Body->addCampo($obPorcAno1,'D');
$obTable->Body->addCampo($obValorAno2, 'D');
$obTable->Body->addCampo($obPorcAno2,'D');
$obTable->Body->addCampo($obValorAno3, 'D');
$obTable->Body->addCampo($obPorcAno3,'D');

$obTable->montaHTML();

$obSpnTable->setValue($obTable->getHtml());

//recupera os valores para as entidades RPPS
$obVLDOTipoEvolucaoPatrimonioLiquido->listEntidadeRPPS($rsRPPS, $_REQUEST);

//adiciona o registro para o totalizador
$rsRPPS->add(array('cod_tipo'       => '99',
                   'nivel'          => '0',
                   'descricao'      => 'TOTAL',
                   'rpps'           => '1',
                   'valor_1'        => '0.00',
                   'valor_2'        => '0.00',
                   'valor_3'        => '0.00',
                   'porcentagem_1'  => '0.00',
                   'porcentagem_2'  => '0.00',
                   'porcentagem_3'  => '0.00',
                   'orcamento_1'    => '0',
                   'orcamento_2'    => '0',
                   'orcamento_3'    => '0'));

//adiciona a formatacao de moeda
$rsRPPS->addFormatacao('valor_1'      ,'NUMERIC_BR');
$rsRPPS->addFormatacao('valor_2'      ,'NUMERIC_BR');
$rsRPPS->addFormatacao('valor_3'      ,'NUMERIC_BR');
$rsRPPS->addFormatacao('porcentagem_1','NUMERIC_BR');
$rsRPPS->addFormatacao('porcentagem_2','NUMERIC_BR');
$rsRPPS->addFormatacao('porcentagem_3','NUMERIC_BR');

$obValorAno1->obEvento->setOnBlur("calculaValor(this.id,'tableRPPS');");
$obValorAno2->obEvento->setOnBlur("calculaValor(this.id,'tableRPPS');");
$obValorAno3->obEvento->setOnBlur("calculaValor(this.id,'tableRPPS');");

//cria a tabela para as entidades rpps
$obTableRPPS = new Table;
$obTableRPPS->setId         ('tableRPPS');
$obTableRPPS->setRecordset  ($rsRPPS);
//$obTableRPPS->setConditional(true, "#efefef");

$obTableRPPS->Head->addCabecalho('Descrição', 25);
$obTableRPPS->Head->addCabecalho(($_REQUEST['slExercicioLDO'] - 2) ,12);
$obTableRPPS->Head->addCabecalho('%',12);
$obTableRPPS->Head->addCabecalho(($_REQUEST['slExercicioLDO'] - 3), 12);
$obTableRPPS->Head->addCabecalho('% ',12);
$obTableRPPS->Head->addCabecalho(($_REQUEST['slExercicioLDO'] - 4), 12);
$obTableRPPS->Head->addCabecalho('%  ',12);

$obTableRPPS->Body->addCampo('[descricao]', 'E');
$obTableRPPS->Body->addCampo($obValorAno1, 'D');
$obTableRPPS->Body->addCampo($obPorcAno1,'D');
$obTableRPPS->Body->addCampo($obValorAno2, 'D');
$obTableRPPS->Body->addCampo($obPorcAno2,'D');
$obTableRPPS->Body->addCampo($obValorAno3, 'D');
$obTableRPPS->Body->addCampo($obPorcAno3,'D');

$obTableRPPS->montaHTML();

$obSpnTableRPPS->setValue($obTableRPPS->getHtml());

//Instancia um objeto Formulario
$obFormulario = new Formulario  ();
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnCodPPA);
$obFormulario->addHidden        ($obHdnAno);

$obFormulario->addSpan          ($obSpnTable);

$obFormulario->addTitulo        ('REGIME PREVIDENCIÁRIO (RPPS)');
$obFormulario->addSpan          ($obSpnTableRPPS);

$obFormulario->Cancelar         ('FLEvolucaoPatrimonioLiquido.php?stAcao=' . $stAcao);
$obFormulario->show             ();

//$jsOnload = 'formataTableReceita();';
//$jsOnload.= 'calculaTotais();';
//$jsOnload.= 'LiberaFrames(true,false);';

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
