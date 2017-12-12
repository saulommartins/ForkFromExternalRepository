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
    * Data de Criação: 12/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 12/10/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTRNDetalhamentoDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTRNDetalhamentoDespesa()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio',Sessao::getExercicio());
}

function montaRecuperaRelacionamento()
{
    $stSql .= "
    \n      SELECT   desp.exercicio
    \n              ,orcamento.orgao.nom_orgao
    \n              ,desp.num_orgao
    \n              ,desp.num_unidade
    \n              ,orcamento.unidade.nom_unidade
    \n              ,desp.cod_funcao
    \n              ,desp.cod_subfuncao
    \n              ,desp.cod_programa
    \n              ,(  SELECT  descricao
    \n                  FROM     orcamento.programa as op
    \n                  WHERE   op.exercicio    = desp.exercicio
    \n                  AND     op.cod_programa = desp.cod_programa
    \n              ) as nom_programa
    \n              ,replace(cont.cod_estrutural,'.','') as estrutural
    \n              ,(  SELECT  nom_pao
    \n                  FROM     orcamento.pao as op
    \n                  WHERE   op.exercicio    = desp.exercicio
    \n                  AND     op.num_pao      = desp.num_pao
    \n              ) as nom_pao
    \n              ,orcamento.fn_consulta_tipo_pao(desp.exercicio,desp.num_pao) as tipo_pao
    \n              ,desp.cod_recurso
    \n              ,(  SELECT  nom_recurso
    \n                  FROM     orcamento.recurso as orr
    \n                  WHERE   orr.exercicio    = desp.exercicio
    \n                  AND     orr.cod_recurso  = desp.cod_recurso
    \n              ) as nom_recurso
    \n              ,desp.vl_original
    \n              ,'' as esfera_anexo /* ??? */
    \n      FROM     orcamento.despesa          as desp
    \n              ,orcamento.conta_despesa    as cont
    \n              ,orcamento.orgao
    \n              ,orcamento.unidade
    \n      WHERE   desp.exercicio = cont.exercicio
    \n      AND     desp.cod_conta = cont.cod_conta
    \n      AND     orcamento.unidade.num_orgao = desp.num_orgao
    \n      AND     orcamento.unidade.num_unidade = desp.num_unidade
    \n      AND     orcamento.unidade.exercicio = desp.exercicio
    \n      AND     orcamento.orgao.num_orgao = orcamento.unidade.num_orgao
    \n      AND     orcamento.orgao.exercicio = orcamento.unidade.exercicio
    \n      AND     desp.exercicio = ".$this->getDado('exercicio')."
    \n      ORDER BY  desp.exercicio
    \n              ,desp.num_orgao
    \n              ,desp.num_unidade
    \n              ,desp.cod_funcao
    \n              ,desp.cod_subfuncao
    \n              ,desp.cod_programa
    \n              ,replace(cont.cod_estrutural,'.','')
    \n              ,desp.cod_recurso

    ";

    return $stSql;
}

}
