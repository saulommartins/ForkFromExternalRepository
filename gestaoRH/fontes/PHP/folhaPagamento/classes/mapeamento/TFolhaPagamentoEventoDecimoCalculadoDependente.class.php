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
    * Classe de mapeamento da tabela folhapagamento.evento_decimo_calculado_dependente
    * Data de Criação: 02/04/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-tabelas

    $Id: TFolhaPagamentoEventoDecimoCalculadoDependente.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.evento_decimo_calculado_dependente
  * Data de Criação: 02/04/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEventoDecimoCalculadoDependente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEventoDecimoCalculadoDependente()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.evento_decimo_calculado_dependente");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,cod_registro,timestamp_registro,desdobramento,cod_dependente');

    $this->AddCampo('cod_evento'        ,'integer'  ,true  ,''      ,true,'TFolhaPagamentoEventoDecimoCalculado');
    $this->AddCampo('cod_registro'      ,'integer'  ,true  ,''      ,true,'TFolhaPagamentoEventoDecimoCalculado');
    $this->AddCampo('timestamp_registro','timestamp',true  ,''      ,true,'TFolhaPagamentoEventoDecimoCalculado');
    $this->AddCampo('desdobramento'     ,'char'     ,true  ,'1'     ,true,'TFolhaPagamentoEventoDecimoCalculado');
    $this->AddCampo('cod_dependente'    ,'integer'  ,true  ,''      ,true,'TPessoalDependente');
    $this->AddCampo('valor'             ,'numeric'  ,true  ,'15,2'  ,false,false);
    $this->AddCampo('quantidade'        ,'numeric'  ,true  ,'15,2'  ,false,false);
    $this->AddCampo('timestamp'         ,'timestamp',true  ,''      ,false,false);

}

}
?>
