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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 19/10/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 19/10/2007

  * @author Analista: Gelson Wolvowski
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/

class TTBAConsContRazao extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDados()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , LPAD(".$this->getDado('unidade_gestora')."::VARCHAR,4,'0') AS unidade_gestora
                        , ".$this->getDado('exercicio')."::VARCHAR||LPAD(".$this->getDado('mes')."::VARCHAR,2,'0') AS competencia
                        , '' AS reservado_tcm
                        , REPLACE(retorno.cod_estrutural, '.', '') AS conta_contabil
                        , ABS(retorno.deb_ex_ant) AS deb_ex_ant
                        , ABS(retorno.deb_mov_ant) AS deb_mov_ant
                        , ABS(retorno.deb_mes) AS deb_mes
                        , ABS(retorno.deb_mov) AS deb_mov
                        , ABS(retorno.cred_ex_ant) AS cred_ex_ant
                        , ABS(retorno.cred_mov_ant) AS cred_mov_ant
                        , ABS(retorno.cred_mes) AS cred_mes
                        , ABS(retorno.cred_mov) AS cred_mov
                        , ABS(retorno.deb_ex) AS deb_ex
                        , ABS(retorno.cred_ex) AS cred_ex

                     FROM tcmba.fn_conscontrazao ('".Sessao::getExercicio()."',
                                                  '".$this->getDado('entidades')."',
                                                  '".$this->getDado('dt_inicial')."',
                                                  '".$this->getDado('dt_final')."',
                                                  '".$this->getDado('dt_final_ant')."') AS retorno

               INNER JOIN contabilidade.plano_conta
                       ON plano_conta.cod_estrutural = retorno.cod_estrutural
                      AND plano_conta.exercicio = '".$this->getDado('exercicio')."'

               INNER JOIN contabilidade.plano_analitica
                       ON plano_analitica.exercicio = plano_conta.exercicio
                      AND plano_analitica.cod_conta = plano_conta.cod_conta

                    ORDER BY retorno.cod_estrutural
                ";
                
        return $stSql;
    }

}