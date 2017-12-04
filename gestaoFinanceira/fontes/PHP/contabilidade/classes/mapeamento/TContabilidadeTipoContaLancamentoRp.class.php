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
    * Classe de mapeamento da tabela contabilidade.tipo_conta_lancamento_rp
    * Data de Criação: 28/12/2006

    * @author Analista: Cleisson Barbosa,
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: bruce $
    $Date: 2006-12-29 10:56:22 -0200 (Sex, 29 Dez 2006) $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  contabilidade.tipo_conta_lancamento_rp
  * Data de Criação: 28/12/2006

  * @author Analista: Cleisson Barbosa,
  * @author Desenvolvedor: Bruce Cruz de Sena

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeTipoContaLancamentoRp extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeTipoContaLancamentoRp()
{
    parent::Persistente();
    $this->setTabela("contabilidade.tipo_conta_lancamento_rp");

    $this->setCampoCod('cod_tipo_conta');
    $this->setComplementoChave('');

    $this->AddCampo('cod_tipo_conta','integer',true,'',true,false);
    $this->AddCampo('descricao','char',true,'character',false,false);

}
}
