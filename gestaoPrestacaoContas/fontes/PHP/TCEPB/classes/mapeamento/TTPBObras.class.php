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
    * Extensão da Classe de mapeamento
    * Data de Criação: 15/04/2008

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 29154 $
    $Name$
    $Author: luiz $
    $Date: 2008-04-11 16:22:17 -0300 (Sex, 11 Abr 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 15/04/2008

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBObras extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBObras()
{
    parent::Persistente();
    $this->setTabela("tcepb.obras");

    $this->setCampoCod('num_obra');
    $this->setComplementoChave('exercicio');

    $this->AddCampo( 'exercicio'            ,'char'     ,true    , '4'    ,true  ,true  );
    $this->AddCampo( 'num_obra'             ,'integer'  ,false   , ''     ,false ,false );
    $this->AddCampo( 'dt_cadastro'          ,'date'     ,false   ,''      ,false );
    $this->AddCampo( 'patrimonio'           ,'char'     ,false   ,'1'     ,false );
    $this->AddCampo( 'localidade'           ,'varchar'  ,false   ,'200'   ,false );
    $this->AddCampo( 'descricao'            ,'varchar'  ,false   ,'300'   ,false );
    $this->AddCampo( 'cod_tipo_obra'        ,'integer'  ,false   ,''      ,false );
    $this->AddCampo( 'cod_tipo_categoria'   ,'integer'  ,false   ,''      ,false );
    $this->AddCampo( 'cod_tipo_fonte'       ,'integer'  ,false   ,''      ,false );
    $this->AddCampo( 'mes_ano_estimado_fim' ,'char'     ,false   ,'6'     ,false );
    $this->AddCampo( 'dt_inicio'            ,'date'     ,false   ,''      ,false );
    $this->AddCampo( 'dt_conclusao'         ,'date'     ,true    ,''      ,false, false );
    $this->AddCampo( 'dt_recebimento'       ,'date'     ,true    ,''      ,false, false );
    $this->AddCampo( 'cod_tipo_situacao'    ,'integer'  ,false   ,''      ,false );
    $this->AddCampo( 'vl_obra'              ,'numeric'  ,false   ,'14,2'  ,false , false );
}

}
