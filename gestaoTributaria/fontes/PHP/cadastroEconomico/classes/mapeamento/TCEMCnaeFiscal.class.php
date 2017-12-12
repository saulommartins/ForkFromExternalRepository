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
  * Classe de mapeamento da tabela ECONOMICO.CNAE_FISCAL
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMCnaeFiscal.class.php 65934 2016-07-01 12:50:13Z evandro $

* Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.CNAE_FISCAL
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMCnaeFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMCnaeFiscal()
{
    parent::Persistente();
    $this->setTabela('economico.cnae_fiscal');

    $this->setCampoCod('cod_cnae');
    $this->setComplementoChave('');

    $this->AddCampo('cod_cnae','integer',true,'',true,false);
    $this->AddCampo('cod_vigencia','integer',true,'',false,true);
    $this->AddCampo('cod_nivel','integer',true,'',false,true);
    $this->AddCampo('cod_estrutural','varchar',true,'160',false,false);
    $this->AddCampo('nom_atividade','varchar',true,'200',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function recuperaCnaeAtivo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCnaeAtivo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaCnaeAtivo()
{
    $stSQL  =" SELECT * FROM                                             
                 (                                                         
                 SELECT                                                    
                     LN.*,                                                 
                     LO.nom_atividade,                                     
                     LO.cod_estrutural as valor_composto,                  
                     publico.fn_mascarareduzida(LO.cod_estrutural) as valor_reduzido, 
                     NI.mascara,                                           
                     NI.nom_nivel                                          
                 FROM                                                      
                     (                                                     
                      SELECT                                               
                          LN.*,                                            
                          LN2.valor                                        
                      FROM (                                               
                            SELECT                                         
                                MAX(LN.cod_nivel) AS cod_nivel,            
                                LN.cod_vigencia ,LN.cod_cnae               
                            FROM                                           
                                economico.nivel_cnae_valor AS LN             
                            WHERE                                          
                                LN.valor <> ''                             
                            GROUP BY                                       
                                LN.cod_vigencia,                           
                                LN.cod_cnae) AS LN,                        
                      economico.nivel_cnae_valor AS LN2                      
                      WHERE                                                
                          LN.cod_nivel       = LN2.cod_nivel AND           
                          LN.cod_cnae        = LN2.cod_cnae  AND           
                          LN.cod_vigencia    = LN2.cod_vigencia            
                     ) AS LN,                                              
                     economico.nivel_cnae AS NI,                             
                     (                                                     
                      SELECT                                               
                          LOC.*                                            
                      FROM                                                 
                          economico.cnae_fiscal AS LOC                       
                     ) AS LO                                               
                 WHERE                                                     
                     LN.cod_nivel       = NI.cod_nivel       AND           
                     LN.cod_vigencia    = NI.cod_vigencia    AND           
                     LN.cod_cnae        = LO.cod_cnae                      
                 ) as tbl                                                  
                                                               
    \n";

    return $stSQL;
}

}
