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
    * Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 16/01/2008
    * 
    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor André Machado
    * 
    * @package URBEM
    * @subpackage
    * 
    * $Id: FTCEMGComparativoPe.class.php 63321 2015-08-18 13:15:45Z franver $
    * $Rev: 63321 $
    * $Author: franver $
    * $Date: 2015-08-18 10:15:45 -0300 (Tue, 18 Aug 2015) $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class FTCEMGComparativoPe extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    
        $this->setTabela('tcemg.fn_comparativoPe');
    
        $this->AddCampo('exercicio'     ,'varchar' ,false ,'' ,false ,false);
        $this->AddCampo('dtInicial'     ,'varchar' ,false ,'' ,false ,false);
        $this->AddCampo('dtFinal'       ,'varchar' ,false ,'' ,false ,false);
        $this->AddCampo('cod_entidade'  ,'varchar' ,false ,'' ,false ,false);
    }
    
    public function montaRecuperaTodos()
    {
        $stSql  = "
              SELECT descricao
                   , ABS(COALESCE(valor,0.00)) AS valor
                FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                           , '".$this->getDado("dtInicial")."'
                                           , '".$this->getDado("dtFinal")."'
                                           , '".$this->getDado("cod_entidade")."'
                                           )
                  AS retorno( descricao VARCHAR
                            , valor     NUMERIC
                            )
        ";
        return $stSql;
    }
    
    public function recuperaContasARO(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContasARO().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
       
    public function montaRecuperaContasARO()
    {
        $stSql = "
              SELECT cod_plano         
                FROM stn.vinculo_contas_rgf_2 
               WHERE cod_conta = 17 
                 AND exercicio = '".$this->getDado("exercicio")."'
        ";
        return $stSql;
    }  
    
    /**
        * Método Destruct
        * @access Private
    */
    public function __destruct() {}
}
