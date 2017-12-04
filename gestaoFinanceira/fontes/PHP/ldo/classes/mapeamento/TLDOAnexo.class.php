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
 * Classe Mapeameto do 02.10.00 - Manter LDO
 * Data de Criação: 12/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.00 - Manter LDO
 */

class TLDOAnexo extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('ldo.anexo');

        $this->setCampoCod('cod_anexo');

        $this->addCampo('cod_anexo', 'integer', true, '', true, true);
        $this->addCampo('cod_acao', 'integer', true, '', false, true);
    }

    public function recuperaListaAnexo(&$rsListaAnexo, $stCriterio = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro       = new Erro();
        $obConexao    = new Conexao();
        $rsListaAnexo = new RecordSet();
        $stSQL        = $this->montaRecuperaListaAnexo($stCriterio, $stOrdem, $boTransacao);

        return $obConexao->executaSQL($rsListaAnexo, $stSQL, $boTransacao);
    }

    public function montaRecuperaListaAnexo($stCriterio, $stOrdem)
    {
        $stSQL  = "      SELECT anexo.cod_anexo                 \n";
        $stSQL .= "           , anexo.cod_acao                  \n";
        $stSQL .= "           , acao.nom_acao                   \n";
        $stSQL .= "       FROM ldo.anexo                        \n";
        $stSQL .= " INNER JOIN administracao.acao               \n";
        $stSQL .= "         ON acao.cod_acao = anexo.cod_acao   \n";
        $stSQL .= $stCriterio . $stOrdem;

        return $stSQL;

    }
}
