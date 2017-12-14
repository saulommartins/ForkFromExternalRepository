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
  * Página de Formulario de Configuração de Subvencoes de Empenho
  * Data de Criação: 26/08/2015
  * @author Analista:      Valtair Santos 
  * @author Desenvolvedor: Evandro Melos
  * $Id: $
  * $Date: $
  * $Author: $
  * $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBASubvencaoEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.subvencao_empenho');
        $this->setCampoCod('numcgm');
        
        //     AddCampo($stNome,$stTipo,$boRequerido='', $nrTamanho='',$boPrimaryKey='',$boForeignKey='',$stConteudo = '')
        $this->AddCampo('numcgm'               , 'integer',   true,   '',   true , true  );
        $this->AddCampo('dt_inicio'            , 'date'   ,   true,   '',   false, false );
        $this->AddCampo('dt_termino'           , 'date'   ,   true,   '',   false, false );
        $this->AddCampo('prazo_aplicacao'      , 'integer',   true,   '',   false, false );
        $this->AddCampo('prazo_comprovacao'    , 'integer',   true,   '',   false, false );
        $this->AddCampo('cod_norma_utilidade'  , 'integer',   true,   '',   false, true  );
        $this->AddCampo('cod_norma_valor'      , 'integer',   true,   '',   false, true  );
        $this->AddCampo('cod_banco'            , 'integer',   true,   '',   false, true  );
        $this->AddCampo('cod_agencia'          , 'integer',   true,   '',   false, true  );
        $this->AddCampo('cod_conta_corrente'   , 'integer',   true,   '',   false, true  );
    }    

    public function recuperaSubvencaoEmpenho(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaSubvencaoEmpenho().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaSubvencaoEmpenho()
    {   
        $stSql = "SELECT   numcgm               
                         , TO_CHAR(dt_inicio,'dd/mm/yyyy') as dt_inicio
                         , TO_CHAR(dt_termino,'dd/mm/yyyy') as dt_termino
                         , prazo_aplicacao      
                         , prazo_comprovacao    
                         , cod_norma_utilidade  
                         , cod_norma_valor      
                         , cod_banco            
                         , cod_agencia          
                         , cod_conta_corrente   
                    FROM tcmba.subvencao_empenho
                   WHERE numcgm = ".$this->getDado('numcgm')." 
                ";
        return $stSql;
    }

}//fim da classe
?>
 