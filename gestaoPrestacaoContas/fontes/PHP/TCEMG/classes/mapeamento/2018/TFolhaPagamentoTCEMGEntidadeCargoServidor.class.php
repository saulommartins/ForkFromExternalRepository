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
* Classe de mapeamento para folhapagamento.tcemg_entidade_cargo_servidor
* Data de Criação: 16/03/2016
* @author Desenvolvedor: Evandro Melos
  $Revision:$
  $Name:$
  $Author:$
  $Date:$
*/

include_once ( CLA_PERSISTENTE );
class TFolhaPagamentoTCEMGEntidadeCargoServidor extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.tcemg_entidade_cargo_servidor');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_tipo, cod_sub_divisao, cod_cargo');

        $this->AddCampo('exercicio'      , 'character', true, '4', true, false);
        $this->AddCampo('cod_tipo'       , 'integer'  , true, '' , true, false);
        $this->AddCampo('cod_sub_divisao', 'integer'  , true, '' , true, false);
        $this->AddCampo('cod_cargo'      , 'integer'  , true, '' , true, false);

        
    }

public function recuperaDadosConfiguracao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosConfiguracao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
} 

   
function montaRecuperaDadosConfiguracao()
{
    $stSql="SELECT DISTINCT 
                tcemg_entidade_cargo_servidor.cod_tipo
                ,regime.cod_regime
                ,sub_divisao.cod_sub_divisao
                ,tcemg_entidade_cargo_servidor.cod_cargo
                ,sub_divisao.descricao as nom_sub_divisao
                ,regime.descricao as nom_regime        
                ,tcemg_entidade_cargo_servidor.cod_cargo||' - '||cargo.descricao as nom_cargo
            FROM folhapagamento.tcemg_entidade_cargo_servidor
            INNER JOIN pessoal.sub_divisao
                ON sub_divisao.cod_sub_divisao = tcemg_entidade_cargo_servidor.cod_sub_divisao 
            INNER JOIN pessoal.regime
                ON regime.cod_regime = sub_divisao.cod_regime
            INNER JOIN pessoal.cargo
                ON cargo.cod_cargo = tcemg_entidade_cargo_servidor.cod_cargo
            
            ";

    return $stSql;
}


public function __destruct(){}

}

?>