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
    * Extensão da Classe de mapeamento TCM-BA tcmba.tipo_alteracao_orcamentaria
    * Data de Criação: 14/09/2015
    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBATipoAlteracaoOrcamentaria extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.tipo_alteracao_orcamentaria');
    
        $this->setCampoCod('cod_tipo');
                                                    
        $this->AddCampo('cod_tipo'  ,'integer' ,true, ''   ,true  ,'','');
        $this->AddCampo('descricao' ,'varchar' ,true, '80' ,false ,'','');
        
    }
    
    function recuperaTipoLimiteAlteracaoCredito(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTipoLimiteAlteracaoCredito().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaTipoLimiteAlteracaoCredito()
    {
        $stSql = " SELECT * FROM tcmba.tipo_alteracao_orcamentaria ";
        return $stSql;
    }

}//End of Class

?>