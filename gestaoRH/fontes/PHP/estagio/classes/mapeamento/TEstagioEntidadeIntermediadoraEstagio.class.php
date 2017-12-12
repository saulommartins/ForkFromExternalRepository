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
    * Classe de mapeamento da tabela estagio.entidade_intermediadora_estagio
    * Data de Criação: 05/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  estagio.entidade_intermediadora_estagio
  * Data de Criação: 05/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEstagioEntidadeIntermediadoraEstagio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEstagioEntidadeIntermediadoraEstagio()
{
    parent::Persistente();
    $this->setTabela("estagio.entidade_intermediadora_estagio");

    $this->setCampoCod('');
    $this->setComplementoChave('cgm_estagiario,cgm_instituicao_ensino,cod_curso,cod_estagio');

    $this->AddCampo('cgm_entidade'          ,'integer',true  ,'',true,'TEstagioEntidadeIntermediadora','numcgm');
    $this->AddCampo('cgm_estagiario'        ,'integer',true  ,'',true,'TEstagioEstagiarioEstagio');
    $this->AddCampo('cgm_instituicao_ensino','integer',true  ,'',true,'TEstagioEstagiarioEstagio');
    $this->AddCampo('cod_curso'             ,'integer',true  ,'',true,'TEstagioEstagiarioEstagio');
    $this->AddCampo('cod_estagio'           ,'integer',true  ,'',true,'TEstagioEstagiarioEstagio');

}
}
?>
