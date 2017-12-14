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
* Classe de Mapeamento para a tabela classificacao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.06.94
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TClassificacao extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('sw_classificacao');
        $this->setCampoCod('cod_classificacao');

        $this->AddCampo('cod_classificacao', 'integer' ,true ,'' ,true  ,false);
        $this->AddCampo('nom_classificacao', 'varchar' ,true ,'' ,false ,false);
    }

    public function recuperaClassificacaoAlteracao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaClassificacaoAlteracao().$stFiltro.$stGroup.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaRecuperaClassificacaoAlteracao()
    {
        $stSql = " SELECT 
                          sw_classificacao.cod_classificacao
                        , sw_classificacao.nom_classificacao 

                     FROM 
                          sw_classificacao

               INNER JOIN sw_processo
                       ON sw_processo.cod_classificacao = sw_classificacao.cod_classificacao
                ";
                
        return $stSql;

    }

    public function recuperaClassificacaoAssunto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaClassificacaoAssunto().$stFiltro.$stGroup.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaRecuperaClassificacaoAssunto()
    {
        $stSql = " SELECT 
                          sw_classificacao.cod_classificacao
                        , sw_classificacao.nom_classificacao 
                        , sw_assunto.cod_assunto
                        , sw_assunto.nom_assunto
                     FROM 
                          sw_classificacao

               INNER JOIN sw_assunto
                       ON sw_assunto.cod_classificacao = sw_classificacao.cod_classificacao
                ";
                
        return $stSql;

    }

}
