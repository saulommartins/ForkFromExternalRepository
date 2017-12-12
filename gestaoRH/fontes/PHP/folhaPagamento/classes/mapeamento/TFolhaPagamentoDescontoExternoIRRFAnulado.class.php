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
    * Classe de mapeamento da tabela folhapagamento.desconto_externo_IRRF_anulado
    * Data de Criação: 07/08/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Finger

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: tiago $
    $Date: 2007-08-07 18:11:47 -0300 (Ter, 07 Ago 2007) $

    * Casos de uso: uc-04.05.60
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.desconto_externo_IRRF_anulado
  * Data de Criação: 07/08/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: Tiago Finger

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoDescontoExternoIRRFAnulado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoDescontoExternoIRRFAnulado()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.desconto_externo_irrf_anulado");

    $this->setCampoCod('');
    $this->setComplementoChave('timestamp,cod_contrato');

    $this->AddCampo('timestamp'        ,'timestamp',true  ,'',true,'TFolhaPagamentoDescontoExternoIRRF');
    $this->AddCampo('cod_contrato'     ,'integer'  ,true  ,'',true,'TFolhaPagamentoDescontoExternoIRRF');
    $this->AddCampo('timestamp_anulado','timestamp_now',true  ,'',false,false);

}
}
?>
