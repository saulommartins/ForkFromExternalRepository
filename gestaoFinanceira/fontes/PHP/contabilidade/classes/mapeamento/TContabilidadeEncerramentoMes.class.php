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
    * Classe de mapeamento da tabela NOVA_CONTABILIDADE.CONFIGURACAO_LANCAMENTO_CREDITO
    * Data de Criação: 24/10/2011

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Davi Aroldi

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.03.03
                    uc-02.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeEncerramentoMes extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TContabilidadeEncerramentoMes()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.encerramento_mes');

        $this->setCampoCod('');
        $this->setComplementoChave('mes,exercicio,timestamp');

        $this->AddCampo('exercicio','char',true,'04',true,false);
        $this->AddCampo('mes','integer',true,'',true,false);
        // $this->AddCampo('timestamp','timestamp',true,'',true,false);
        $this->AddCampo('situacao','char',true,'01',false,false);

    }

    public function recuperaEncerramentoMes(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaEncerramentoMes().$stFiltro.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        $this->setDebug($stSql);

        return $obErro;
    }

    public function montaRecuperaEncerramentoMes()
    {
        $stSql = "
            SELECT mes
                 , exercicio
                 , situacao
              FROM contabilidade.encerramento_mes
             WHERE timestamp = ( SELECT MAX(timestamp)
                                   FROM contabilidade.encerramento_mes em
                                  WHERE em.mes = encerramento_mes.mes
                                    AND em.exercicio = encerramento_mes.exercicio )
        ";
        if ( $this->getDado('mes') ) {
            $stSql .= " AND mes = ".$this->getDado('mes');
        }

        if ( $this->getDado('exercicio') ) {
            $stSql .= " AND exercicio = '".$this->getDado('exercicio')."' ";
        }

        if ( $this->getDado('situacao') ) {
            $stSql .= " AND situacao = '".$this->getDado('situacao')."' ";
        }

        return $stSql;
    }
}

?>
