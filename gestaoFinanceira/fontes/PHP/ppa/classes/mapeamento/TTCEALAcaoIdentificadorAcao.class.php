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
    * Classe de mapeamento 
    * Data de Criação: 12/11/2014

    * @author Analista: Silvia Martins
    * @author Desenvolvedor: Evandro Melos
    *
    * $Id: TTCEALAcaoIdentificadorAcao.class.php 61014 2014-11-28 17:21:36Z franver $
*/

class TTCEALAcaoIdentificadorAcao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALAcaoIdentificadorAcao()
    {
        parent::Persistente();

        $this->setTabela('tceal.acao_identificador_acao');

        $this->setCampoCod('cod_acao');

        $this->AddCampo('cod_acao'          , 'integer'  , true, ''  , true , true );
        $this->AddCampo('cod_identificador' , 'integer'  , true, ''  , false , true );
        
    }
    
    public function recuperaAcaoIdentificadorAcao(&$rsRecordSet, $stFiltro, $stOrder, $boTransacao)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAcaoIdentificadorAcao().$stFiltro.$stOrder;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaAcaoIdentificadorAcao()
    {
        $stSql = "  SELECT *
                    FROM tceal.identificador_acao
                    JOIN tceal.acao_identificador_acao
                        ON acao_identificador_acao.cod_identificador = identificador_acao.cod_identificador
                    JOIN ppa.acao
                        ON acao.cod_acao = acao_identificador_acao.cod_acao
                ";

        return $stSql;
    }

 } // fim da classe
