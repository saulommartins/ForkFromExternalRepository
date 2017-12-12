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
* Classe de Mapeamento para tabela cgm_pessoa_fisica
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27796 $
$Name$
$Author: rodrigosoares $
$Date: 2008-01-28 16:04:26 -0200 (Seg, 28 Jan 2008) $

Casos de uso: uc-01.02.92, uc-01.02.93
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCGMPessoaFisica extends Persistente
{
    public function TCGMPessoaFisica()
    {
        parent::Persistente();
        $this->setTabela('sw_cgm_pessoa_fisica');
        $this->setCampoCod('numcgm');

        $this->AddCampo('numcgm',            'integer', true, '', true,  true);
        $this->AddCampo('cod_categoria_cnh', 'integer', true, '', false, true);
        $this->AddCampo('rg',                'varchar', true, 10, false, false);
        $this->AddCampo('dt_emissao_rg',     'date',    false, '', false, false);
        $this->AddCampo('orgao_emissor',     'varchar', true, 20, false, false);
        $this->AddCampo('cpf',               'varchar', false, 11, false, false);
        $this->AddCampo('num_cnh',           'varchar', true, 15, false, false);
        $this->AddCampo('dt_validade_cnh',   'date',    false, '', false, false);
        $this->AddCampo('cod_nacionalidade', 'integer', true, '', false, true);
        $this->AddCampo('cod_escolaridade',  'integer', false, '', false, true);
        $this->AddCampo('dt_nascimento',     'date',    false,'',false,false);
        $this->AddCampo('sexo',              'varchar', false, 1,false,false);
        $this->AddCampo('servidor_pis_pasep','varchar', false, 15,false,false);
    }

function montaRecuperaRelacionamento()
{
    $stSql  = "    SELECT CGM.*,                                  \n";
    $stSql .= "           UF.*,                                   \n";
    $stSql .= "           PF.*,                                   \n";
    $stSql .= "           CH.nom_categoria as nom_cnh,            \n";
    $stSql .= "           MUNI.*                                  \n";
    $stSql .= "      FROM sw_cgm AS CGM                           \n";
    $stSql .= "      JOIN sw_uf AS UF                             \n";
    $stSql .= "        ON UF.cod_uf = CGM.cod_uf                  \n";
    $stSql .= "      JOIN sw_municipio AS MUNI                    \n";
    $stSql .= "        ON MUNI.cod_uf = CGM.cod_uf                \n";
    $stSql .= "       AND MUNI.cod_municipio = CGM.cod_municipio  \n";
    $stSql .= "      JOIN sw_cgm_pessoa_fisica AS PF              \n";
    $stSql .= "        ON CGM.numcgm = PF.numcgm                  \n";
    $stSql .= " LEFT JOIN sw_categoria_habilitacao AS CH          \n";
    $stSql .= "        ON CH.cod_categoria = PF.cod_categoria_cnh \n";
    $stSql .= "     WHERE CGM.numcgm <> 0                         \n";

    return $stSql;
}

}

?>
