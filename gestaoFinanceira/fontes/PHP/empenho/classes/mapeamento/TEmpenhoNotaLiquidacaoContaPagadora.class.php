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
    * Classe de mapeamento da tabela empenho.nota_liquidacao_conta_pagadora
    * Data de Criação: 11/10/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: leandro.zis $
    $Date: 2007-09-05 18:59:08 -0300 (Qua, 05 Set 2007) $

    * Casos de uso: uc-02.03.03
*/
/*
$Log$
Revision 1.2  2007/09/05 21:53:18  leandro.zis
esfinge

Revision 1.1  2006/10/11 17:27:39  cako
Inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  empenho.nota_liquidacao_conta_pagadora
  * Data de Criação: 11/10/2006

  * @author Analista: Cleisson Barboza
  * @author Desenvolvedor: Anderson C. Konze

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoNotaLiquidacaoContaPagadora extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoNotaLiquidacaoContaPagadora()
{
    parent::Persistente();
    $this->setTabela("empenho.nota_liquidacao_conta_pagadora");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio_liquidacao,cod_entidade,cod_nota,timestamp');

    $this->AddCampo('exercicio_liquidacao','char'     ,false ,'04'  ,true,'TEmpenhoNotaLiquidacaoPaga','exercicio');
    $this->AddCampo('cod_entidade'        ,'integer'  ,false ,''    ,true,'TEmpenhoNotaLiquidacaoPaga','cod_entidade');
    $this->AddCampo('cod_nota'            ,'integer'  ,false ,''    ,true,'TEmpenhoNotaLiquidacaoPaga','cod_nota');
    $this->AddCampo('timestamp'           ,'timestamp',false ,''    ,true,'TEmpenhoNotaLiquidacaoPaga');
    $this->AddCampo('exercicio'           ,'char'     ,false ,'4'   ,false,'TContabilidadePlanoAnalitica');
    $this->AddCampo('cod_plano'           ,'integer'  ,false ,''    ,false,'TContabilidadePlanoAnalitica');

}

function montaRecuperaDesembolsoEsfinge()
{
    $stSql  = "
select nota_liquidacao_conta_pagadora.cod_entidade
      ,nota_liquidacao_conta_pagadora.exercicio
      ,nota_liquidacao.cod_empenho
      ,to_char(nota_liquidacao_paga.timestamp, 'dd/mm/yyyy') as timestamp
      ,plano_conta.cod_estrutural
      ,sum(nota_liquidacao_paga.vl_pago) as vl_pago
from empenho.nota_liquidacao_conta_pagadora
join empenho.nota_liquidacao_paga
  on nota_liquidacao_paga.exercicio = nota_liquidacao_conta_pagadora.exercicio_liquidacao
 and nota_liquidacao_paga.cod_entidade = nota_liquidacao_conta_pagadora.cod_entidade
 and nota_liquidacao_paga.cod_nota = nota_liquidacao_conta_pagadora.cod_nota
 and nota_liquidacao_paga.timestamp = nota_liquidacao_conta_pagadora.timestamp
join contabilidade.plano_analitica
  on plano_analitica.exercicio = nota_liquidacao_conta_pagadora.exercicio
 and plano_analitica.cod_plano = nota_liquidacao_conta_pagadora.cod_plano
join contabilidade.plano_conta
  on plano_conta.exercicio = plano_analitica.exercicio
 and plano_conta.cod_conta = plano_analitica.cod_conta
join empenho.nota_liquidacao
  on nota_liquidacao_conta_pagadora.exercicio = nota_liquidacao.exercicio
 and nota_liquidacao_conta_pagadora.cod_entidade = nota_liquidacao.cod_entidade
 and nota_liquidacao_conta_pagadora.cod_nota = nota_liquidacao.cod_nota
where nota_liquidacao_paga.cod_entidade in (".$this->getDado('cod_entidade').")
  and nota_liquidacao_paga.exercicio = '".$this->getDado('exercicio')."'
  and nota_liquidacao_paga.timestamp between to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')
  and to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')
group by nota_liquidacao_conta_pagadora.cod_entidade
      ,nota_liquidacao_conta_pagadora.exercicio
      ,nota_liquidacao.cod_empenho
      ,nota_liquidacao_paga.timestamp
      ,plano_conta.cod_estrutural
";

  return $stSql;

}

/**
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDesembolsoEsfinge(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDesembolsoEsfinge();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
