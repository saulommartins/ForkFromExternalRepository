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
* Classe de mapeamento para folhapagamento.tcemg_entidade_remuneracao
* Data de Criação: 16/03/2016
* @author Desenvolvedor: Evandro Melos
  $Revision:$
  $Name:$
  $Author:$
  $Date:$
*/

include_once ( CLA_PERSISTENTE );

class TFolhaPagamentoTCEMGEntidadeRemuneracao extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.tcemg_entidade_remuneracao');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_tipo, cod_evento');

        $this->AddCampo('exercicio'   , 'character', true, '4', true, false);
        $this->AddCampo('cod_tipo'    , 'integer'  , true, '' , true, false);
        $this->AddCampo('cod_evento'  , 'integer'  , true, '' , false, false);
        
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
    $stSql="SELECT 
                tcemg_entidade_remuneracao.cod_tipo
                ,tcemg_entidade_remuneracao.cod_evento
                ,evento.codigo||' - '||evento.descricao as nom_evento
            FROM folhapagamento.tcemg_entidade_remuneracao
            INNER JOIN folhapagamento.evento
                ON evento.cod_evento = tcemg_entidade_remuneracao.cod_evento
                        
            ";

    return $stSql;
}

   
public function __destruct(){}

}

?>