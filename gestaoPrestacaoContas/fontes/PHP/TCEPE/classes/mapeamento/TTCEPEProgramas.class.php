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
    * 
    * Data de Criação   : 01/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPEProgramas.class.php 60291 2014-10-10 18:07:42Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEProgramas extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEProgramas()
    {
        parent::Persistente();
    }


    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaArquivoTCEPEProgramas.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaArquivoTCEPEProgramas(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaArquivoTCEPEProgramas().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaArquivoTCEPEProgramas()
    {
        $stSql = "  SELECT 
                             programa.num_programa
                            , SUBSTR(TRIM(programa_dados.identificacao), 0, 100) AS denominacao 
                            , SUBSTR(programa_dados.objetivo, 0, 255) AS objetivo                                   
                    FROM ppa.programa
                    
                    JOIN ppa.programa_dados
                         ON programa_dados.cod_programa = programa.cod_programa
                        AND programa_dados.timestamp_programa_dados = (SELECT MAX(timestamp_programa_dados) 
                                                                        FROM ppa.programa_dados 
                                                                        WHERE programa_dados.cod_programa = programa.cod_programa)

                    JOIN orcamento.programa_ppa_programa
                        ON programa_ppa_programa.cod_programa_ppa = programa.cod_programa

                    JOIN orcamento.programa as op
                         ON op.exercicio        = programa_ppa_programa.exercicio
                        AND op.cod_programa = programa_ppa_programa.cod_programa

                    WHERE op.exercicio = '".$this->getDado('exercicio')."'

                    ORDER BY programa_dados.cod_programa
                    
                ";
        return $stSql;
    }

}
?>