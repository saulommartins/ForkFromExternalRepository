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

class TLDONotaExplicativa extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('ldo.nota_explicativa');

        $this->setCampoCod('cod_nota_explicativa');

        $this->addCampo('cod_nota_explicativa', 'integer', true, '', true, true);
        $this->addCampo('descricao', 'text', false, '', false, false);
        $this->addCampo('numcgm', 'integer', true, '', false, true);
        $this->addCampo('cod_anexo', 'integer', true, '', false, true);
        $this->addCampo('ano', 'character', true, '4', false, true);
    }

    public function recuperaNotaExplicativa(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        $stSQL       = $this->montaRecuperaNotaExplicativa($stFiltro);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    public function montaRecuperaNotaExplicativa($stFiltro)
    {
        $stSQL  = "     SELECT ne.cod_nota_explicativa,      \n";
        $stSQL .= "            ne.descricao                  \n";
        $stSQL .= "       FROM ldo.nota_explicativa ne       \n";
        $stSQL .= " INNER JOIN ldo.anexo a                   \n";
        $stSQL .= "         ON ne.cod_anexo = a.cod_anexo    \n";

        return $stSQL . $stFiltro;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL  = "    SELECT ne.cod_nota_explicativa       \n";
        $stSQL .= "         , ne.descricao                  \n";
        $stSQL .= "         , ne.numcgm                     \n";
        $stSQL .= "         , cgm.nom_cgm                   \n";
        $stSQL .= "         , ne.cod_anexo                  \n";
        $stSQL .= "         , aa.nom_acao                   \n";
        $stSQL .= "         , ne.ano                        \n";
        $stSQL .= "         , e.cod_entidade                \n";
        $stSQL .= "      FROM ldo.nota_explicativa AS ne    \n";
        $stSQL .= "INNER JOIN orcamento.entidade AS e       \n";
        $stSQL .= "        ON e.numcgm = ne.numcgm          \n";
        $stSQL .= "INNER JOIN sw_cgm AS cgm                 \n";
        $stSQL .= "        ON cgm.numcgm = ne.numcgm        \n";
        $stSQL .= "INNER JOIN ldo.anexo AS a                \n";
        $stSQL .= "        ON a.cod_anexo = ne.cod_anexo    \n";
        $stSQL .= "INNER JOIN administracao.acao AS aa      \n";
        $stSQL .= "        ON aa.cod_acao = a.cod_acao      \n";

        return $stSQL;
    }
}
