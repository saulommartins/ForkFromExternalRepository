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
    * Classe de mapeamento PPA.PPA_NORMA
    * Data de Criação: 03/10/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.12
*/

class TPPAPPANorma extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ppa.ppa_norma');

        $this->setCampoCod('cod_ppa');
        $this->setComplementoChave('cod_norma');

        $this->AddCampo('cod_ppa', 'integer', true, '', true, true);
        $this->AddCampo('cod_norma', 'integer', true, '', true, true);
        $this->AddCampo('timestamp', 'timestamp', false, '', false, false);
    }

    public function recuperaPPANorma(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        $stFiltro .= ' GROUP BY ppa.cod_ppa ';
        $stFiltro .= '        , ppa.ano_inicio ';
        $stFiltro .= '        , ppa.timestamp  ';
        $stFiltro .= '        , ppa.ano_inicio ';
        $stFiltro .= '        , ppa.ano_final  ';

        return $this->executaRecupera("montaRecuperaPPANorma", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaPPANorma()
    {
        $stSql = "	   SELECT ppa.cod_ppa									    \n";
        $stSql.= "		    , ppa.ano_inicio 								    \n";
        $stSql.= "		    , ppa.ano_final           						    \n";
        $stSql.= "		    , ppa.timestamp           						    \n";
        $stSql.= "		    , ppa.ano_inicio||' a '||ppa.ano_final AS periodo	\n";
        $stSql.= "		    , MAX(ppa_publicacao.timestamp) AS ts_homologacao   \n";
        $stSql.= "	     FROM ppa.ppa       					                \n";
        $stSql.= "  LEFT JOIN ppa.ppa_publicacao							    \n";
        $stSql.= " 		   ON ppa_publicacao.cod_ppa = ppa.cod_ppa 		        \n";
        $stSql.= " 		WHERE ppa.fn_verifica_homologacao(ppa.cod_ppa) = false  \n";

        return $stSql;
    }
 } // end of class
