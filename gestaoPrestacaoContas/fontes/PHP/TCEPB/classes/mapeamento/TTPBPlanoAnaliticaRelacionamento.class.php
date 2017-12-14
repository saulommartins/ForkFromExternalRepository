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
    * Classe de mapeamento da tabela
    * Data de Criação: 24/01/2007

    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59774 $
    $Name$
    $Autor:$
    $Date: 2014-09-10 14:37:03 -0300 (Wed, 10 Sep 2014) $

    * Casos de uso: uc-06.03.00

*/

/*
$Log$
Revision 1.2  2007/05/14 20:10:31  hboaventura
Arquivos para geração do TCEPB

Revision 1.1  2007/05/11 15:11:22  hboaventura
Arquivos para geração do TCEPB

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBPlanoAnaliticaRelacionamento extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TTPBPlanoAnaliticaRelacionamento()
    {
        parent::Persistente();
        $this->setTabela("tcepb.plano_analitica_relacionamento");

        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('exercicio,tipo');

        $this->AddCampo( 'cod_plano'          ,'integer' ,true  , ''   ,true  ,true  );
        $this->AddCampo( 'exercicio'          ,'char'    ,true  , '4'  ,true  ,true  );
        $this->AddCampo( 'tipo'               ,'char'    ,true  , '1'  ,false ,false );
        $this->AddCampo( 'cod_relacionamento' ,'integer' ,true  , ''   ,false ,false );
    }

    public function recuperaRelacionamentoReceita(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamentoReceita",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamentoReceita()
    {
        $stSql = "
            SELECT  substr(replace(plano_conta.cod_estrutural,'.',''),1,9) as cod_estrutural
                 ,  plano_analitica_relacionamento.cod_relacionamento AS cod_relacionamento_receita
              FROM  tcepb.plano_analitica_relacionamento
        INNER JOIN  contabilidade.plano_analitica
                ON  plano_analitica.cod_plano = plano_analitica_relacionamento.cod_plano
               AND  plano_analitica.exercicio = plano_analitica_relacionamento.exercicio
        INNER JOIN  contabilidade.plano_conta
                ON  plano_conta.cod_conta = plano_analitica.cod_conta
               AND  plano_conta.exercicio = plano_analitica.exercicio
             WHERE  plano_analitica_relacionamento.exercicio = '".$this->getDado('exercicio')."'
               AND  plano_analitica_relacionamento.tipo = 'R'
          GROUP BY  substr(replace(plano_conta.cod_estrutural,'.',''),1,9)
                 ,  plano_analitica_relacionamento.cod_relacionamento
        ";

        return $stSql;
    }
    
    public function recuperaRelacionamentoDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamentoDespesa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamentoDespesa()
    {
        $stSql = "
            SELECT  substr(replace(plano_conta.cod_estrutural,'.',''),1,9) as cod_estrutural
                 ,  plano_analitica_relacionamento.cod_relacionamento AS cod_relacionamento_despesa
              FROM  tcepb.plano_analitica_relacionamento
        INNER JOIN  contabilidade.plano_analitica
                ON  plano_analitica.cod_plano = plano_analitica_relacionamento.cod_plano
               AND  plano_analitica.exercicio = plano_analitica_relacionamento.exercicio
        INNER JOIN  contabilidade.plano_conta
                ON  plano_conta.cod_conta = plano_analitica.cod_conta
               AND  plano_conta.exercicio = plano_analitica.exercicio
             WHERE  plano_analitica_relacionamento.exercicio = '".$this->getDado('exercicio')."'
               AND  plano_analitica_relacionamento.tipo = 'D'
          GROUP BY  substr(replace(plano_conta.cod_estrutural,'.',''),1,9)
                 ,  plano_analitica_relacionamento.cod_relacionamento
        ";

        return $stSql;
    }

    public function recuperaContaAnalitica(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContaAnalitica",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContaAnalitica()
    {
        
        $stSQL  =" SELECT                                                   
                         plano_analitica.cod_plano,                                        
                         plano_conta.exercicio,                                        
                         plano_conta.cod_conta,                                        
                         plano_conta.nom_conta,                                        
                         plano_conta.cod_estrutural,                                  
                         relacionamento_receita.cod_relacionamento AS cod_relacionamento_receita,
                         relacionamento_despesa.cod_relacionamento AS cod_relacionamento_despesa,
                         plano_analitica_tipo_retencao.cod_tipo
                         
                     FROM                                                     
                         contabilidade.plano_conta
                         
                     JOIN                 
                          contabilidade.plano_analitica
                       ON plano_conta.cod_conta  = plano_analitica.cod_conta 
                      AND plano_conta.exercicio  = plano_analitica.exercicio    
                          
                LEFT JOIN (SELECT * 
                             FROM tcepb.plano_analitica_relacionamento
                            WHERE tipo = 'R' 
                          ) AS relacionamento_receita
                       ON relacionamento_receita.cod_plano = plano_analitica.cod_plano
                      AND relacionamento_receita.exercicio = plano_analitica.exercicio
             
             LEFT JOIN (SELECT * 
                            FROM tcepb.plano_analitica_relacionamento
                           WHERE tipo = 'D' 
                        ) AS relacionamento_despesa
                       ON relacionamento_despesa.cod_plano = plano_analitica.cod_plano
                      AND relacionamento_despesa.exercicio = plano_analitica.exercicio
             
             LEFT JOIN
                        tcepb.plano_analitica_tipo_retencao
                    ON  tcepb.plano_analitica_tipo_retencao.cod_plano = plano_analitica.cod_plano
                   AND  tcepb.plano_analitica_tipo_retencao.exercicio = plano_analitica.exercicio
                   
                 WHERE                            
                        plano_conta.cod_conta  = plano_analitica.cod_conta 
                   AND plano_conta.exercicio  = plano_analitica.exercicio
        ";
        
        return $stSQL;
    }

    public function recuperaContaComLancamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContaComLancamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContaComLancamento()
    {
        $stSql = "
                SELECT  conta_debito.cod_plano
                        FROM  contabilidade.conta_debito
                  INNER JOIN  contabilidade.plano_analitica
                          ON  plano_analitica.cod_plano = conta_debito.cod_plano
                         AND  plano_analitica.exercicio = conta_debito.exercicio
                       WHERE  conta_debito.cod_plano = ".$this->getDado('cod_plano')."
                         AND  conta_debito.exercicio = '".$this->getDado('exercicio')."'

                       UNION

                      SELECT  conta_credito.cod_plano
                        FROM  contabilidade.conta_credito
                  INNER JOIN  contabilidade.plano_analitica
                          ON  plano_analitica.cod_plano = conta_credito.cod_plano
                         AND  plano_analitica.exercicio = conta_credito.exercicio
                       WHERE  conta_credito.cod_plano = ".$this->getDado('cod_plano')."
                         AND  conta_credito.exercicio = '".$this->getDado('exercicio')."'
                       LIMIT  1
        ";

        return $stSql;
    }

}
