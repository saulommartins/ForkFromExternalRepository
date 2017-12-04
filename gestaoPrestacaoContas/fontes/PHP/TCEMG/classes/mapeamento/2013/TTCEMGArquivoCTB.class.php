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
    * Data de Criação: 15/04/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Lisiane Morais

    $Id: TTCEMGRespLic.class.php 61907 2015-03-13 16:49:31Z michel $

   
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGArquivoCTB extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGArquivoCTB()
    {
        parent::Persistente();
        $this->setTabela('tcemg.arquivo_ctb');
        $this->setCampoCod('cod_ctb_view');
        //$this->setComplementoChave('');
       
        $this->AddCampo('cod_ctb_view'                  , 'VARCHAR',  true,    '20',   true,  false);
        $this->AddCampo('ano'                           , 'VARCHAR',  true,   '4',   false, false);
        $this->AddCampo('cod_ctb'                       , 'INTEGER',  true,    '',   false, false);
        $this->AddCampo('cod_orgao'                     , 'INTEGER',  true,    '',   false, false);
        $this->AddCampo('tipo_conta'                    , 'INTEGER',  true,    '',   false, false);
        $this->AddCampo('tipo_aplicacao'                , 'VARCHAR',  false,    '',   false, false);
        $this->AddCampo('mes'                           , 'INTEGER',  true,    '',   false, false);
    }
    
    public function recuperaArquivoCTB(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaArquivoCTB($stFiltro).$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    
    public function montaRecuperaArquivoCTB($stFiltro)
    {            
        $stSql = " SELECT retorno.cod_ctb
                                 , retorno.tipo_conta||regexp_replace((retorno.conta), '[-|,|.|x]', '', 'gi') AS cod_ctb_view 
                                 , retorno.cod_orgao
                                 , retorno.tipo_conta
                                 , retorno.tipo_aplicacao
                                 , ".$this->getDado('mes')." AS mes
                                 , '".$this->getDado('exercicio')."' AS exercicio
                              FROM tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as retorno
                                                    (  cod_conta                         INTEGER
							,tipo_aplicacao                    VARCHAR
                                                        ,cod_ctb                           INTEGER
                                                        ,tipo_conta                        INTEGER
                                                        ,exercicio                         CHAR(4)  
                                                        ,conta                             TEXT                         
                                                        ,conta_bancaria                    TEXT  
                                                        ,conta_corrente                    TEXT                                                         
                                                        ,cod_orgao                         INTEGER                                                        
                                                        ,banco                             VARCHAR                                                        
                                                        ,agencia                           TEXT                                                        
                                                        ,digito_verificador_agencia        TEXT                                                        
                                                        ,digito_verificador_conta_bancaria TEXT                                                        
                                                        ,desc_conta_bancaria               VARCHAR     
                                                        --,cod_plano                      integer              
                                                   )
                          INNER JOIN tcemg.arquivo_ctb
                               ON arquivo_ctb.mes != ".$this->getDado('mes')."
                         GROUP BY retorno.cod_ctb
                                , retorno.cod_orgao
                                , retorno.tipo_conta
                                , retorno.tipo_aplicacao
                                , conta
                         ORDER BY cod_ctb ";

        return $stSql;
    }
    public function __destruct(){}
}
?>