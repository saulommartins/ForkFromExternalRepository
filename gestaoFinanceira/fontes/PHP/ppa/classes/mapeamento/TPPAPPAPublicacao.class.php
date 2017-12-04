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
    * Classe de mapeamento PPA.PPA_PUBLICACAO
    * Data de Criação: 03/10/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.12
*/

class TPPAPPAPublicacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ppa.ppa_publicacao');

        $this->setCampoCod('cod_ppa');
        $this->setComplementoChave('');

        $this->AddCampo('cod_ppa'       , 'integer'  , true, '', true, true);
        $this->AddCampo('numcgm_veiculo', 'integer'  , true, '', false, true);
        $this->AddCampo('timestamp'     , 'timestamp', true, '', false, true);
        $this->AddCampo('cod_norma'     , 'integer'  , true, '', false, true);
    }

   /* function recuperaPPA(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="") {
        return $this->executaRecupera("montaRecuperaPPA", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaPPA()
    {
        $stSql = "	select ppa.cod_ppa													\n";
        $stSql.= "		,ppa.pre_inclusao 	       										\n";
        $stSql.= "		,ppa.ano_inicio 											    	\n";
        $stSql.= "		,ppa.ano_final           											\n";
        $stSql.= "		,ppa.ano_inicio||' a '||ppa.ano_final as periodo	\n";
        $stSql.= "	from ppa.ppa								                 			\n";

        return $stSql;
    }*/
 } // end of class
