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
    * Classe de mapeamento da tabela licitacao.contrato_anulado
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 17482 $
    $Name$
    $Author: larocca $
    $Date: 2006-11-08 08:51:42 -0200 (Qua, 08 Nov 2006) $

    * Casos de uso: uc-03.05.22
*/
/*
$Log$
Revision 1.2  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/11/01 19:56:38  leandro.zis
atualizado

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.contrato_anulado
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoContratoAnulado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function TLicitacaoContratoAnulado()
{
    parent::Persistente();
    $this->setTabela("licitacao.contrato_anulado");

    $this->setCampoCod('');
    $this->setComplementoChave('num_contrato,exericicio,cod_entidade');

    $this->AddCampo('num_contrato','integer',true,''   ,true,'TLicitacaoContrato');
    $this->AddCampo('exercicio','char',true,'4'  ,true,'TLicitacaoContrato');
    $this->AddCampo('cod_entidade','integer',true,'',true,'TLicitacaoContrato');
    $this->AddCampo('dt_anulacao','date',true,'',true,false);
    $this->AddCampo('motivo','text',true,''  ,true,false);
    $this->AddCampo('valor_anulacao','numeric',true,'14,2',false,false);
  }

}
