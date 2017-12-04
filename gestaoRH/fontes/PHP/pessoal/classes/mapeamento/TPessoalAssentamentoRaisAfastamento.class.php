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
    * Classe de mapeamento da tabela pessoal.assentamento_rais_afastamento
    * Data de Criação: 25/10/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TPessoalAssentamentoRaisAfastamento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.04.12
*/
/*
$Log: base.php,v $
Revision 1.3  2007/07/25 13:47:01  souzadl
alterado

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.assentamento_rais_afastamento
  * Data de Criação: 25/10/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoRaisAfastamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoRaisAfastamento()
{
    parent::Persistente();
    $this->setTabela("pessoal.assentamento_rais_afastamento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento,timestamp');

    $this->AddCampo('cod_assentamento','integer'  ,true  ,'',true,'TPessoalAssentamentoAfastamentoTemporario');
    $this->AddCampo('timestamp'       ,'timestamp',true  ,'',true,'TPessoalAssentamentoAfastamentoTemporario');
    $this->AddCampo('cod_rais'        ,'integer'  ,true  ,'',false,'TPessoalRaisAfastamento');

}
}
?>
