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
    * Classe de mapeamento da tabela TCEMG.CONFIGURACAO_LEIS_LDO
    * Data de Criação: 15/01/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: $
    *
    * $Name: $
    * $Date: $
    * $Author: $
    * $Rev: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGConfiguracaoLeisLDO extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGConfiguracaoLeisLDO()
    {
        parent::Persistente();
        $this->setTabela('tcemg.configuracao_leis_ldo');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_norma, tipo_configuracao');

        $this->AddCampo('exercicio'        , 'varchar', true, '4',  true, false);
        $this->AddCampo('cod_norma'        , 'integer', true,  '',  true,  true);
        $this->AddCampo('tipo_configuracao', 'varchar', true,  '', false, false);
        $this->AddCampo('status'           , 'boolean', true,  '', false, false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql .= "SELECT configuracao_leis_ldo.*                                          \n";
        $stSql .= "     , norma.cod_tipo_norma                                              \n";
        $stSql .= "     , norma.nom_norma                                                   \n";
        $stSql .= "     , tipo_norma.nom_tipo_norma                                         \n";
        $stSql .= "  FROM tcemg.configuracao_leis_ldo       \n";
        $stSql .= "     , normas.norma                                                      \n";
        $stSql .= "     , normas.tipo_norma                                                 \n";
        $stSql .= " WHERE configuracao_leis_ldo.cod_norma = norma.cod_norma             \n";
        $stSql .= "   AND norma.cod_tipo_norma = tipo_norma.cod_tipo_norma                  \n";

        return $stSql;
    }
    
    public function __destruct(){}

}

?>
