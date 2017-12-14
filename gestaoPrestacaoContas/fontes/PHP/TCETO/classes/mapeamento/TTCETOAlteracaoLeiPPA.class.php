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
    * Pacote de configuração do TCETO - Mapeamento tceto.alteracao_lei_ppa
    * Data de Criação   : 12/11/2014
    *
    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Evandro Melos
    * $Id: TTCETOAlteracaoLeiPPA.class.php 61442 2015-01-16 16:55:25Z lisiane $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCETOAlteracaoLeiPPA extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOAlteracaoLeiPPA()
    {
        parent::Persistente();
        $this->setTabela("tceto.alteracao_lei_ppa");

        $this->setCampoCod('cod_norma');
        $this->setComplementoChave('data_alteracao, timestamp');

        $this->AddCampo( 'cod_norma'      ,'integer'   ,true  , ''   ,true  ,true  );
        $this->AddCampo( 'data_alteracao' ,'date'      ,true  , ''  ,true  ,false );
        $this->AddCampo( 'timestamp'      ,'timestamp' ,false , ''   ,true  ,false );
    } 

    public function recuperaAlteracaoLeisPPA(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY") === false) ? " ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaAlteracaoLeisPPA().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaAlteracaoLeisPPA()
    {
        $stSql = "  SELECT alteracao_lei_ppa.cod_norma
                        , alteracao_lei_ppa.data_alteracao
                        , tipo_norma.nom_tipo_norma||' '||norma.num_norma||'/'||norma.exercicio||' - '||norma.nom_norma  AS nom_norma
                        , norma.descricao
                        , norma.exercicio
                        , norma.num_norma
                        , norma.cod_tipo_norma
                        , tipo_norma.nom_tipo_norma
                    FROM tceto.alteracao_lei_ppa 
                    JOIN normas.norma
                        ON norma.cod_norma = alteracao_lei_ppa.cod_norma
                    JOIN normas.tipo_norma
                      ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma
                    WHERE alteracao_lei_ppa.timestamp = (SELECT MAX(timestamp) 
                                                         FROM tceto.alteracao_lei_ppa)
                ";

        return $stSql;
    }

}
?>