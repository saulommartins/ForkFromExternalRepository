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
    * Classe de mapeamento da tabela folhapagamento.configuracao_empenho_lla_atributo
    * Data de Criação: 10/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 10:02:38 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-04.05.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.configuracao_empenho_lla_atributo
  * Data de Criação: 10/07/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoEmpenhoLlaAtributo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoConfiguracaoEmpenhoLlaAtributo()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.configuracao_empenho_lla_atributo");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_atributo,cod_modulo,cod_cadastro,exercicio,cod_configuracao_lla,timestamp');

    $this->AddCampo('cod_atributo'        ,'integer'  ,true  ,''   ,true ,'TAdministracaoAtributoDinamico');
    $this->AddCampo('cod_modulo'          ,'integer'  ,true  ,''   ,true ,'TAdministracaoAtributoDinamico');
    $this->AddCampo('cod_cadastro'        ,'integer'  ,true  ,''   ,true ,'TAdministracaoAtributoDinamico');
    $this->AddCampo('exercicio'           ,'char'     ,true  ,'4'  ,true ,'TFolhaPagamentoConfiguracaoEmpenhoLla');
    $this->AddCampo('cod_configuracao_lla','integer'  ,true  ,''   ,true ,'TFolhaPagamentoConfiguracaoEmpenhoLla');
    $this->AddCampo('timestamp'           ,'timestamp',true  ,''   ,true ,'TFolhaPagamentoConfiguracaoEmpenhoLla');

}
}
?>
