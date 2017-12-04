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
    * Classe de mapeamento da tabela compras.publicacao_compra_direta
    * Data de Criação: 03/08/2015

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: $
    $Name$
    $Author:$
    $Date: $

    * Casos de uso: uc-03.05.17
    *
     $Id:$
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );


class TComprasPublicacaoCompraDireta extends Persistente
{
  /**
      * Método Construtor
      * @access Private
  */
  public function __construct()
  {
    parent::Persistente();
    $this->setTabela("compras.publicacao_compra_direta");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_compra_direta,cod_entidade,exercicio_entidade,cod_modalidade');
    
    $this->AddCampo('cod_compra_direta' ,'integer',false ,''   ,true  ,true);
    $this->AddCampo('cod_entidade'      ,'integer',false ,''   ,true  ,true);
    $this->AddCampo('cod_modalidade'    ,'integer',false ,''   ,true  ,true);
    $this->AddCampo('exercicio_entidade','char'   ,false ,'4'  ,true  ,true);
    $this->AddCampo('cgm_veiculo'       ,'integer',false ,''   ,false ,true);
    $this->AddCampo('data_publicacao'   ,'date'   ,false  ,''  ,false ,false);
    $this->AddCampo('observacao'        ,'varchar',false ,''   ,false ,false);
    $this->AddCampo('num_publicacao'    ,'integer',false ,''   ,false,false);

  }

  public function recuperaVeiculosPublicacao(&$rsRecordSet,  $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaVeiculosPublicacao($numEdital).$stFiltro.$stOrdem;
      $this->stDebug = $stSql;
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
  }

  public function montaRecuperaVeiculosPublicacao()
  {
    //minha tabela fisica
    $tab = 'compras.publicacao_compra_direta';

    $stSql .=" SELECT sw_cgm.numcgm as veiculoPublicacao              
                    , sw_cgm.nom_cgm as nomeVeiculoPublicacao           
                    , to_char(compras.publicacao_compra_direta.data_publicacao,'dd/mm/yyyy') as dataPublicacao       
                    , compras.publicacao_compra_direta.observacao as observacao
                    , compras.publicacao_compra_direta.num_publicacao as num_publicacao            
                 FROM compras.publicacao_compra_direta,sw_cgm                                        
                WHERE sw_cgm.numcgm=compras.publicacao_compra_direta.cgm_veiculo \n";
    if ( $this->getDado( 'cod_compra_direta' ) ) {
      $stSql .="    AND ".$tab.".cod_compra_direta=".$this->getDado( 'cod_compra_direta' )."  \n";
    }
    if ( $this->getDado( 'exercicio_entidade' ) ) {
      $stSql .="    AND ".$tab.".exercicio_entidade='".$this->getDado( 'exercicio_entidade' )."'  \n";
    }
    return $stSql;
  }

}
