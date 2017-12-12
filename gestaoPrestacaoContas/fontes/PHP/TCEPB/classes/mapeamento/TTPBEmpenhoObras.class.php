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
    * Data de Criação: 26/04/2008

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
  * Data de Criação: 26/04/2008

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBEmpenhoObras extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBEmpenhoObras()
{
    parent::Persistente();
    $this->setTabela("tcepb.empenho_obras");

    $this->setComplementoChave('exercicio_empenho, cod_entidade, cod_empenho');

    $this->AddCampo ('exercicio_obras'      ,'char',   true, '4', true, true);
    $this->AddCampo ('num_obra'             ,'integer',true, '' , true, true);
    $this->AddCampo ('exercicio_empenho'    ,'char',   true, '4', true, true);
    $this->AddCampo ('cod_entidade'         ,'integer',true, '' , true, true);
    $this->AddCampo ('cod_empenho'          ,'integer',true, '' , true, true);

    $this->setDado  ('exercicio', Sessao::getExercicio() );
}

function recuperaLista(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaLista",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaLista()
{
    $stSql = "
        SELECT   obr.*
                ,emo.*
                ,cgm.nom_cgm
        FROM     tcepb.empenho_obras    as emo
                ,empenho.empenho        as emp
                ,orcamento.entidade     as ent
                ,sw_cgm                 as cgm
                ,tcepb.obras            as obr
        WHERE   emo.exercicio_empenho   = emp.exercicio
        AND     emo.cod_entidade        = emp.cod_entidade
        AND     emo.cod_empenho         = emp.cod_empenho

        AND     emo.exercicio_obras     = obr.exercicio
        AND     emo.num_obra            = obr.num_obra

        AND     emp.exercicio           = ent.exercicio
        AND     emp.cod_entidade        = ent.cod_entidade

        AND     ent.numcgm              = cgm.numcgm
        AND     emp.exercicio           = '".$this->getDado('exercicio')."'
        ORDER BY emo.exercicio_empenho, emo.cod_entidade, emo.cod_empenho, emo.exercicio_obras, emo.num_obra
    ";

    return $stSql;
}

}
