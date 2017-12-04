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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEALConfiguracaoBalanceteDespRecExtra extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TTCEALConfiguracaoBalanceteDespRecExtra()
    {
        parent::Persistente();
        $this->setTabela("tceal.despesa_receita_extra");

        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('exercicio');

        $this->AddCampo( 'cod_plano' ,'integer' ,true, ''   ,true ,true  );
        $this->AddCampo( 'exercicio' ,'char' ,true, '4'   ,true ,true  );
        $this->AddCampo( 'classificacao' ,'char' ,true, '2' ,false ,false );
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT  despesa_receita_extra.cod_plano
                 ,  tipo_lancamento
                 ,  cod_estrutural
                 ,  nom_conta
              FROM  tceal.despesa_receita_extra
        INNER JOIN  contabilidade.plano_analitica
                ON  plano_analitica.cod_plano = despesa_receita_extra.cod_plano
               AND  plano_analitica.exercicio = despesa_receita_extra.exercicio
        INNER JOIN  contabilidade.plano_conta
                ON  plano_conta.cod_conta = plano_analitica.cod_conta
               AND  plano_conta.exercicio = plano_analitica.exercicio
             WHERE  despesa_receita_extra.exercicio = '".$this->getDado('exercicio')."' ";
        if ( $this->getDado('tipo_lancamento') ) {
            $stSql.= " AND despesa_receita_extra.tipo_lancamento = ".$this->getDado('tipo_lancamento')." ";
        }
        if ( $this->getDado('sub_tipo_lancamento') ) {
            if ($this->getDado('sub_tipo_lancamento') > 4) {
                $stSql.= " AND  despesa_receita_extra.sub_tipo_lancamento > 4 ";
            } else {
                $stSql.= " AND  despesa_receita_extra.sub_tipo_lancamento = ".$this->getDado('sub_tipo_lancamento')." ";
            }
        }
        if ( $this->getDado('categoria') ) {
            $stSql.= " AND  despesa_receita_extra.categoria = ".$this->getDado('categoria')." ";
        }
        if ( $this->getDado('cod_plano') ) {
            $stSql.= " AND  despesa_receita_extra.cod_plano = ".$this->getDado('cod_plano')." ";
        }

        return $stSql;
    }

    public function recuperaUltimoSequencialSubTipo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaUltimoSequencialSubTipo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaUltimoSequencialSubTipo()
    {
        $stSql = "
            SELECT  max(sub_tipo_lancamento) as max_sub_tipo
              FROM  tceal.despesa_receita_extra
        WHERE  despesa_receita_extra.exercicio = '".$this->getDado('exercicio')."' ";
        if ( $this->getDado('tipo_lancamento') ) {
            $stSql.= " AND despesa_receita_extra.tipo_lancamento = ".$this->getDado('tipo_lancamento')." ";
        }
        if ( $this->getDado('sub_tipo_lancamento') ) {
            $stSql.= " AND  despesa_receita_extra.sub_tipo_lancamento = ".$this->getDado('sub_tipo_lancamento')." ";
        }
        if ( $this->getDado('categoria') ) {
            $stSql.= " AND  despesa_receita_extra.categoria = ".$this->getDado('categoria')." ";
        }
        if ( $this->getDado('cod_plano') ) {
            $stSql.= " AND  despesa_receita_extra.cod_plano = ".$this->getDado('cod_plano')." ";
        }

        return $stSql;
    }

}
?>
