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
    * Classe de mapeamento da tabela folhapagamento.configuracao_eventos_desconto_externo
    * Data de Criação: 25/07/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Finger

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: tiago $
    $Date: 2007-08-10 11:45:41 -0300 (Sex, 10 Ago 2007) $

    * Casos de uso: uc-04.05.61
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.configuracao_eventos_desconto_externo
  * Data de Criação: 25/07/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: Tiago Finger

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoEventosDescontoExterno extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoConfiguracaoEventosDescontoExterno()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.configuracao_eventos_desconto_externo");

    $this->setCampoCod('cod_configuracao');
    $this->setComplementoChave('timestamp');

    $this->AddCampo('cod_configuracao'           ,'sequence'     ,true  ,'',true,false);
    $this->AddCampo('timestamp'                  ,'timestamp_now',true  ,'',true,false);
    $this->AddCampo('evento_desconto_irrf'       ,'integer'      ,true  ,'',false,'TFolhaPagamentoEvento');
    $this->AddCampo('evento_base_irrf'           ,'integer'      ,true  ,'',false,'TFolhaPagamentoEvento');
    $this->AddCampo('evento_desconto_previdencia','integer'      ,true  ,'',false,'TFolhaPagamentoEvento');
    $this->AddCampo('evento_base_previdencia'    ,'integer'      ,true  ,'',false,'TFolhaPagamentoEvento');

}
}
?>
