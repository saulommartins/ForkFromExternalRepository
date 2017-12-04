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

/**
 * Classe de mapeamento da tabela tcepe.tipo_norma
 * Data de Criação: 07/10/2014
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
 * @package URBEM
 * @subpackage Mapeamento
 $Id: $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPETipoNorma extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TTCEPETipoNorma()
    {
        parent::Persistente();
        $this->setTabela('tcepe.tipo_norma');
        $this->setCampoCod('cod_tipo');

        $this->AddCampo('cod_tipo'  , 'integer' , true  , ''   , true  ,false);
        $this->AddCampo('descricao' , 'varchar' , false , '20' , false ,false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql .= "
         SELECT tipo_norma.*

           FROM tcepe.tipo_norma

       ORDER BY tipo_norma.cod_tipo ";

        return $stSql;
    }
}
