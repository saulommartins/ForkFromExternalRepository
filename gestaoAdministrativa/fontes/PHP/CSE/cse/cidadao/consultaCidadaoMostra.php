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
* Arquivo de instância para manutenção de Cidadão
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 4925 $
$Name$
$Author: lizandro $
$Date: 2006-01-11 10:44:22 -0200 (Qua, 11 Jan 2006) $

* Casos de uso: uc-01.07.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"       );
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php"   );
  include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"   ); //Inclui classe para inserir auditoria
  include_once '../cse.class.php';
  include_once (CAM_FW_LEGADO."configuracaoLegado.class.php");
  include_once (CAM_FW_LEGADO."mascarasLegado.lib.php"      );

if ( !isset($pagina)) {
    $pagina = "0";
}

if ($_GET["consultar"] == "true") {
    $ctrl = 0;
    unset($sessao->transf4);
    $sessao->transf4 = array();
    $stSql  = " SELECT "                                    .$stQuebra;
    $stSql .= "     cod_cidadao, "                          .$stQuebra;
    $stSql .= "     cod_grandeza_moradia, "                 .$stQuebra;
    $stSql .= "     cod_unidade_moradia, "                  .$stQuebra;
    $stSql .= "     cod_deficiencia, "                      .$stQuebra;
    $stSql .= "     cod_sexo, "                             .$stQuebra;
    $stSql .= "     cod_raca, "                             .$stQuebra;
    $stSql .= "     cod_estado_civil, "                     .$stQuebra;
    $stSql .= "     cod_tipo_certidao, "                    .$stQuebra;
    $stSql .= "     cod_grau_parentesco, "                  .$stQuebra;
    $stSql .= "     cod_municipio_origem, "                 .$stQuebra;
    $stSql .= "     cod_uf_origem, "                        .$stQuebra;
    $stSql .= "     cod_uf_certidao, "                      .$stQuebra;
    $stSql .= "     cod_uf_rg, "                            .$stQuebra;
    $stSql .= "     cod_uf_ctps, "                          .$stQuebra;
    $stSql .= "     nom_cidadao, "                          .$stQuebra;
    $stSql .= "     telefone_celular, "                     .$stQuebra;
    $stSql .= "     TO_CHAR(dt_nascimento,'DD/MM/YYYY') AS dt_nascimento, ".$stQuebra;
    $stSql .= "     pais_origem, "                          .$stQuebra;
    $stSql .= "     TO_CHAR(dt_entrada_pais,'DD/MM/YYYY') AS dt_entrada_pais, " .$stQuebra;
    $stSql .= "     num_identificacao_social, "             .$stQuebra;
    $stSql .= "     num_termo_certidao, "                   .$stQuebra;
    $stSql .= "     num_livro_certidao, "                   .$stQuebra;
    $stSql .= "     num_folha_certidao, "                   .$stQuebra;
    $stSql .= "     TO_CHAR(dt_emissao_certidao,'DD/MM/YYYY') AS dt_emissao_certidao, ".$stQuebra;
    $stSql .= "     nom_cartorio_certidao, "                .$stQuebra;
    $stSql .= "     num_cartao_saude, "                     .$stQuebra;
    $stSql .= "     num_rg, "                               .$stQuebra;
    $stSql .= "     complemento_rg, "                       .$stQuebra;
    $stSql .= "     orgao_emissor_rg, "                     .$stQuebra;
    $stSql .= "     TO_CHAR(dt_emissao_rg,'DD/MM/YYYY') AS dt_emissao_rg, ".$stQuebra;
    $stSql .= "     num_ctps, "                             .$stQuebra;
    $stSql .= "     serie_ctps, "                           .$stQuebra;
    $stSql .= "     TO_CHAR(dt_emissao_ctps,'DD/MM/YYYY') AS dt_emissao_ctps, ".$stQuebra;
    $stSql .= "     num_cpf, "                              .$stQuebra;
    $stSql .= "     num_titulo_eleitor, "                   .$stQuebra;
    $stSql .= "     zona_titulo_eleitor, "                  .$stQuebra;
    $stSql .= "     secao_titulo_eleitor, "                 .$stQuebra;
    $stSql .= "     pis_pasep, "                            .$stQuebra;
    $stSql .= "     cbo_r, "                                .$stQuebra;
    $stSql .= "     vl_salario, "                           .$stQuebra;
    $stSql .= "     vl_aposentadoria, "                     .$stQuebra;
    $stSql .= "     vl_seguro_desemprego, "                 .$stQuebra;
    $stSql .= "     vl_pensao_alimenticia, "                .$stQuebra;
    $stSql .= "     vl_outras_rendas, "                     .$stQuebra;
    $stSql .= "     tempo_moradia, "                        .$stQuebra;
    $stSql .= "     pessoa_responsavel, "                   .$stQuebra;
    $stSql .= "     mes_gestacao, "                         .$stQuebra;
    $stSql .= "     amamentando, "                          .$stQuebra;
    $stSql .= "     qtd_filhos, "                           .$stQuebra;
    $stSql .= "     nom_pai, "                              .$stQuebra;
    $stSql .= "     nom_mae "                               .$stQuebra;
    $stSql .= " FROM "                                      .$stQuebra;
    $stSql .= "     cse.cidadao "                       .$stQuebra;
    $stSql .= " WHERE "                                     .$stQuebra;
    $stSql .= "     cod_cidadao = ".$codCidadao." "         .$stQuebra;
    $conn = new dataBaseLegado;
    $conn->abreBD();
    $conn->abreSelecao($stSql);
    $conn->fechaBD();
    $conn->vaiPrimeiro();
    if (!$conn->eof()) {
        $sessao->transf4[cidadao][codCidadao]                     = $codCidadao;
        $sessao->transf4[cidadao][pagina]                         = $pagina;
        $sessao->transf4[cidadao][codUnidadeMedida]               = $conn->pegaCampo("cod_unidade_moradia")."-".$conn->pegaCampo("cod_grandeza_moradia");
        $sessao->transf4[cidadao][codDeficiencia]                 = $conn->pegaCampo("cod_deficiencia");
        $sessao->transf4[cidadao][sexoCidadao]                    = $conn->pegaCampo("cod_sexo");
        $sessao->transf4[cidadao][codRacaCidadao]                 = $conn->pegaCampo("cod_raca");
        $sessao->transf4[cidadao][codEstadoCivil]                 = $conn->pegaCampo("cod_estado_civil");
        $sessao->transf4[documentacao][codCertidao]               = $conn->pegaCampo("cod_tipo_certidao");
        $sessao->transf4[vinculo][codGrauParentesco]              = $conn->pegaCampo("cod_grau_parentesco");
        $sessao->transf4[cidadao][municipio]                      = $conn->pegaCampo("cod_municipio_origem");
        $sessao->transf4[cidadao][estado]                         = $conn->pegaCampo("cod_uf_origem");
        $sessao->transf4[documentacao][uf]                        = $conn->pegaCampo("cod_uf_certidao");
        $sessao->transf4[documentacao][ufRg]                      = $conn->pegaCampo("cod_uf_rg");
        $sessao->transf4[documentacao][ufCtps]                    = $conn->pegaCampo("cod_uf_ctps");
        $sessao->transf4[cidadao][nomCgm]                         = $conn->pegaCampo("nom_cidadao");
        $sessao->transf4[cidadao][telCelular]                     = $conn->pegaCampo("telefone_celular");
        $sessao->transf4[cidadao][dataNasc]                       = $conn->pegaCampo("dt_nascimento");
        $sessao->transf4[cidadao][paisOrCidadao]                  = $conn->pegaCampo("pais_origem");
        $sessao->transf4[documentacao][dataEntradaBrasil]         = $conn->pegaCampo("dt_entrada_pais");
        $sessao->transf4[documentacao][numIdentSocial]            = $conn->pegaCampo("num_identificacao_social");
        $sessao->transf4[documentacao][numTermo]                  = $conn->pegaCampo("num_termo_certidao");
        $sessao->transf4[documentacao][numLivro]                  = $conn->pegaCampo("num_livro_certidao");
        $sessao->transf4[documentacao][numFolha]                  = $conn->pegaCampo("num_folha_certidao");
        $sessao->transf4[documentacao][dataEmissao]               = $conn->pegaCampo("dt_emissao_certidao");
        $sessao->transf4[documentacao][nomCartorio]               = $conn->pegaCampo("nom_cartorio_certidao");
        $sessao->transf4[documentacao][numCartSaude]              = $conn->pegaCampo("num_cartao_saude");
        $sessao->transf4[documentacao][rg]                        = $conn->pegaCampo("num_rg");
        $sessao->transf4[documentacao][complementoRg]             = $conn->pegaCampo("complemento_rg");
        $sessao->transf4[documentacao][orgaoEmissor]              = $conn->pegaCampo("orgao_emissor_rg");
        $sessao->transf4[documentacao][dataEmissaoRg]             = $conn->pegaCampo("dt_emissao_rg");
        $sessao->transf4[documentacao][numCtps]                   = $conn->pegaCampo("num_ctps");
        $sessao->transf4[documentacao][serieCtps]                 = $conn->pegaCampo("serie_ctps");
        $sessao->transf4[documentacao][dataEmissaoCtps]           = $conn->pegaCampo("dt_emissao_ctps");
        $sessao->transf4[documentacao][cpf]                       = $conn->pegaCampo("num_cpf");
        $sessao->transf4[documentacao][numTitEleitor]             = $conn->pegaCampo("num_titulo_eleitor");
        $sessao->transf4[documentacao][zonaEleitor]               = $conn->pegaCampo("zona_titulo_eleitor");
        $sessao->transf4[documentacao][secaoEleitor]              = $conn->pegaCampo("secao_titulo_eleitor");
        $sessao->transf4[documentacao][pis]                       = $conn->pegaCampo("pis_pasep");
        $sessao->transf4[documentacao][cbor]                      = $conn->pegaCampo("cbo_r");
        $sessao->transf4[profissionais][vlrSalario]               = $conn->pegaCampo("vl_salario");
        $sessao->transf4[profissionais][vlrAposentadoria]         = $conn->pegaCampo("vl_aposentadoria");
        $sessao->transf4[profissionais][vlrSegdesmprego]          = $conn->pegaCampo("vl_seguro_desemprego");
        $sessao->transf4[profissionais][vlrPensaoAlimenticia]     = $conn->pegaCampo("vl_pensao_alimenticia");
        $sessao->transf4[profissionais][vlrOutrasRendas]          = $conn->pegaCampo("vl_outras_rendas");
        $sessao->transf4[cidadao][tempoMoradia]                   = $conn->pegaCampo("tempo_moradia");
        $sessao->transf4[vinculo][respCrianca]                    = $conn->pegaCampo("pessoa_responsavel");
        $sessao->transf4[vinculo][mesGestacao]                    = $conn->pegaCampo("mes_gestacao");
        $sessao->transf4[vinculo][amamentando]                    = $conn->pegaCampo("amamentando");
        $sessao->transf4[vinculo][qtdFilhos]                      = $conn->pegaCampo("qtd_filhos");
        $sessao->transf4[cidadao][nomPai]                         = $conn->pegaCampo("nom_pai");
        $sessao->transf4[cidadao][nomMae]                         = $conn->pegaCampo("nom_mae");
    }
    $conn->limpaSelecao();

    $stSql  = " SELECT "                                       .$stQuebra;
    $stSql .= "     d.cod_domicilio AS cod_domicilio, "        .$stQuebra;
    $stSql .= "     d.logradouro AS logradouro "               .$stQuebra;
    $stSql .= " FROM "                                         .$stQuebra;
    $stSql .= "     cse.cidadao_domicilio as cd, "             .$stQuebra;
    $stSql .= "     cse.domicilio    as d "                    .$stQuebra;
    $stSql .= " WHERE "                                        .$stQuebra;
    $stSql .= "     d.cod_domicilio = cd.cod_domicilio AND "   .$stQuebra;
    $stSql .= "     cd.cod_cidadao = ".$codCidadao." "         .$stQuebra;
    $conn->abreBD();
    $conn->abreSelecao($stSql);
    $conn->fechaBD();
    $conn->vaiPrimeiro();
    if (!$conn->eof()) {
        $sessao->transf4[cidadao][codDomicilio] = $conn->pegaCampo("cod_domicilio");
        $sessao->transf4[cidadao][nomDomicilio] = $conn->pegaCampo("logradouro");
    }
    $conn->limpaSelecao();

    $stSql  = " SELECT "                                            .$stQuebra;
    $stSql .= "     qe.cod_grau AS cod_grau, "                      .$stQuebra;
    $stSql .= "     qe.cod_instituicao AS cod_instituicao, "        .$stQuebra;
    $stSql .= "     qe.cod_cidadao AS cod_cidadao, "                .$stQuebra;
    $stSql .= "     qe.dt_cadastro AS dt_cadastro, "                .$stQuebra;
    $stSql .= "     qe.serie AS serie, "                            .$stQuebra;
    $stSql .= "     qe.frequencia AS frequencia, "                  .$stQuebra;
    $stSql .= "     ie.nom_instituicao AS nom_instituicao, "        .$stQuebra;
    $stSql .= "     ge.nom_grau AS nom_grau "                       .$stQuebra;
    $stSql .= " FROM "                                              .$stQuebra;
    $stSql .= "     cse.qualificacao_escolar qe, "                  .$stQuebra;
    $stSql .= "     cse.instituicao_educacional as ie, "            .$stQuebra;
    $stSql .= "     cse.grau_escolar ge "                           .$stQuebra;
    $stSql .= " WHERE "                                             .$stQuebra;
    $stSql .= "     qe.cod_grau = ge.cod_grau AND "                 .$stQuebra;
    $stSql .= "     qe.cod_instituicao = ie.cod_instituicao AND "   .$stQuebra;
    $stSql .= "     qe.cod_cidadao = ".$codCidadao." "              .$stQuebra;
    $conn->abreBD();
    $conn->abreSelecao($stSql);
    $conn->fechaBD();
    $conn->vaiPrimeiro();
    if (!$conn->eof()) {
        $sessao->transf4[escolaridade][grauInstrucao]   = $conn->pegaCampo("cod_grau");
        $sessao->transf4[escolaridade][nomInstrucao]    = $conn->pegaCampo("nom_grau");
        $sessao->transf4[escolaridade][instEducacional] = $conn->pegaCampo("cod_instituicao");
        $sessao->transf4[escolaridade][nomEducacional]  = $conn->pegaCampo("nom_instituicao");
        $sessao->transf4[escolaridade][serie]           = $conn->pegaCampo("serie");
        $sessao->transf4[escolaridade][frequancia]      = $conn->pegaCampo("frequencia");
    }
    $conn->limpaSelecao();

    $stSql  = " SELECT "                                                    .$stQuebra;
    $stSql .= "      qp.cod_profissao AS cod_profissao, "                   .$stQuebra;
    $stSql .= "      pro.nom_profissao AS nom_profissao, "                  .$stQuebra;
    $stSql .= "      qp.cod_empresa AS cod_empresa, "                       .$stQuebra;
    $stSql .= "      qp.cod_cidadao AS cod_cidadao, "                       .$stQuebra;
    $stSql .= "      TO_CHAR(qp.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro, " .$stQuebra;
    $stSql .= "      TO_CHAR(qp.dt_admissao,'DD/MM/YYYY') AS dt_admissao, " .$stQuebra;
    $stSql .= "      qp.emprego_atual AS emprego_atual, "                   .$stQuebra;
    $stSql .= "      qp.ocupacao AS ocupacao, "                             .$stQuebra;
    $stSql .= "      emp.nom_empresa AS nom_empresa, "                      .$stQuebra;
    $stSql .= "      emp.cnpj AS cnpj, "                                    .$stQuebra;
    $stSql .= "      pro.nom_profissao AS nom_profissao "                   .$stQuebra;
    $stSql .= " FROM "                                                      .$stQuebra;
    $stSql .= "     cse.qualificacao_profissional AS qp, "                  .$stQuebra;
    $stSql .= "     cse.empresa AS emp, "                                   .$stQuebra;
    $stSql .= "     cse.profissao AS pro "                                  .$stQuebra;
    $stSql .= " WHERE "                                                     .$stQuebra;
    $stSql .= "     qp.cod_profissao = pro.cod_profissao AND "              .$stQuebra;
    $stSql .= "     qp.cod_empresa   = emp.cod_empresa AND "                .$stQuebra;
    $stSql .= "     qp.cod_cidadao   = ".$codCidadao." "                    .$stQuebra;
    $conn->abreBD();
    $conn->abreSelecao($stSql);
    $conn->fechaBD();
    $conn->vaiPrimeiro();

    if (!$conn->eof()) {
        $sessao->transf4[profissionais][codProfissao]   = $conn->pegaCampo("cod_profissao");
        $sessao->transf4[profissionais][nomProfissao]   = $conn->pegaCampo("nom_profissao");
        $sessao->transf4[profissionais][codEmpresa]     = $conn->pegaCampo("cod_empresa");
        $sessao->transf4[profissionais][nomEmpresa]     = $conn->pegaCampo("nom_empresa");
        $sessao->transf4[profissionais][dataAdmissao]   = $conn->pegaCampo("dt_admissao");
        $sessao->transf4[profissionais][empregado]      = $conn->pegaCampo("emprego_atual");
        $sessao->transf4[profissionais][ocupacao]       = $conn->pegaCampo("ocupacao");
        if ( $conn->pegaCampo("cnpj") ) {
            $sessao->transf4[profissionais][cnpj]           = geraMascaraCNPJ($conn->pegaCampo("cnpj"));
        } else {
            $sessao->transf4[profissionais][cnpj]           = "";
        }
    }
    $conn->limpaSelecao();

    $stSql  = " SELECT "                                                    .$stQuebra;
    $stSql .= "      * "                                                    .$stQuebra;
    $stSql .= " FROM "                                                      .$stQuebra;
    $stSql .= "     cse.responsavel "                                   .$stQuebra;
    $stSql .= " WHERE "                                                     .$stQuebra;
    $stSql .= "     cod_cidadao   = ".$codCidadao." "                       .$stQuebra;
    $conn->abreBD();
    $conn->abreSelecao($stSql);
    $conn->fechaBD();
    $conn->vaiPrimeiro();

    if (!$conn->eof()) {
        $sessao->transf4[despesas][vlrAluguel]           = number_format($conn->pegaCampo("vl_aluguel"),2, ",", ".");
        $sessao->transf4[despesas][vlrDespCasaPropria]   = number_format($conn->pegaCampo("vl_prestacao_habitacional"),2, ",", ".");
        $sessao->transf4[despesas][vlrDespAlimentacao]   = number_format($conn->pegaCampo("vl_alimentacao"),2, ",", ".");
        $sessao->transf4[despesas][vlrDespAgua]          = number_format($conn->pegaCampo("vl_agua"),2, ",", ".");
        $sessao->transf4[despesas][vlrDespTransporte]    = number_format($conn->pegaCampo("vl_transporte"),2, ",", ".");
        $sessao->transf4[despesas][vlrDespEnergia]       = number_format($conn->pegaCampo("vl_luz"),2, ",", ".");
        $sessao->transf4[despesas][vlrDespRemedio]       = number_format($conn->pegaCampo("vl_remedio"),2, ",", ".");
        $sessao->transf4[despesas][vlrDespGas]           = number_format($conn->pegaCampo("vl_gas"),2, ",", ".");
        $sessao->transf4[despesas][vlrDespDiversas]      = number_format($conn->pegaCampo("vl_despesas_diversas"),2, ",", ".");
        $sessao->transf4[despesas][qtdDependentes]       = $conn->pegaCampo("num_dependentes");
        $sessao->transf4[cidadao][numCgm]                = $conn->pegaCampo("numcgm");
        $sessao->transf4[cidadao][respDomicilio]         = "t";
    } else {
        $sessao->transf4[cidadao][respDomicilio]         = "f";
    }
    $conn->limpaSelecao();

    $stSql  = " SELECT "                                    .$stQuebra;
    $stSql .= "     qc.nom_questao, "                       .$stQuebra;
    $stSql .= "     qc.valor_padrao, "                      .$stQuebra;
    $stSql .= "     qc.tipo, "                              .$stQuebra;
    $stSql .= "     rc.* "                                  .$stQuebra;
    $stSql .= " FROM "                                      .$stQuebra;
    $stSql .= "     cse.questao_censo as qc, "          .$stQuebra;
    $stSql .= "     cse.resposta_censo as rc "          .$stQuebra;
    $stSql .= " WHERE "                                     .$stQuebra;
    $stSql .= "     qc.cod_questao = rc.cod_questao and "   .$stQuebra;
    $stSql .= "     rc.cod_cidadao = ".$codCidadao." "      .$stQuebra;
    $stSql .= " ORDER BY "                                  .$stQuebra;
    $stSql .= "     rc.cod_questao, "                       .$stQuebra;
    $stSql .= "     rc.cod_resposta, "                      .$stQuebra;
    $stSql .= "     rc.exercicio "                          .$stQuebra;
    $conn->abreBD();
    $conn->abreSelecao($stSql);
    $conn->fechaBD();
    $conn->vaiPrimeiro();

    while (!$conn->eof()) {
        $indice = $conn->pegaCampo("exercicio")."_".$conn->pegaCampo("cod_questao");
        if ( $conn->pegaCampo("tipo") == "m" ) {
            $indice = "questaoCenso[".$indice."][".$conn->pegaCampo("resposta")."]";
            $sessao->transf4[censo][$indice] = $conn->pegaCampo("resposta");
        } else {
            $indice = "questaoCenso[".$indice."]";
            $sessao->transf4[censo][$indice] = $conn->pegaCampo("resposta");
        }
        $conn->vaiProximo();
    }
    $conn->limpaSelecao();

    $stSql  = " SELECT "                                                .$stQuebra;
    $stSql .= "     cod_programa, "                                     .$stQuebra;
    $stSql .= "     exercicio, "                                        .$stQuebra;
    $stSql .= "     cod_cidadao, "                                      .$stQuebra;
    $stSql .= "     TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao, " .$stQuebra;
    $stSql .= "     vl_beneficio, "                                     .$stQuebra;
    $stSql .= "     prioritario "                                       .$stQuebra;
    $stSql .= " FROM "                                                  .$stQuebra;
    $stSql .= "     cse.cidadao_programa "                              .$stQuebra;
    $stSql .= " WHERE "                                                 .$stQuebra;
    $stSql .= "     cod_cidadao = ".$codCidadao." "                     .$stQuebra;
    $conn->abreBD();
    $conn->abreSelecao($stSql);
    $conn->fechaBD();
    $conn->vaiPrimeiro();
    while (!$conn->eof()) {
        $exercicio = trim($conn->pegaCampo("exercicio"));
        $indice = "ps[".$exercicio."_".$conn->pegaCampo("cod_programa")."_";
        $sessao->transf4[programas][$indice."cod]"]  = "true";
        $sessao->transf4[programas][$indice."di]"]   = $conn->pegaCampo("dt_inclusao");
        $sessao->transf4[programas][$indice."vl]"]   = number_format($conn->pegaCampo("vl_beneficio"),2, ",", ".");
        if (  $conn->pegaCampo("prioritario") == "t" ) {
            $sessao->transf4[programas][$indice."bp]"]   = "true";
        }
        $conn->vaiProximo();
    }
    $conn->limpaSelecao();
}

$arAba = array (
                0 => "Cidadão/domicílio",
                1 => "Documentação",
                2 => "Escolaridade",
                3 => "Dados profissionais",
                4 => "Despesas Mensais",
                5 => "Vínculo familiar",
                6 => "Censo",
                7 => "Programas Sociais"
                );

$arAbaAtual = array (
                    0 => "cidadao",
                    1 => "documentacao",
                    2 => "escolaridade",
                    3 => "profissionais",
                    4 => "despesas",
                    5 => "vinculo",
                    6 => "censo",
                    7 => "programas"
                    );

function geraNomeVar($nome, $valor)
{
    global $aba;
    global $sessao;
    if ( is_array( $valor ) ) {
        foreach ($valor as $stNome => $stValor) {
            $nomeNovo = $nome."[".$stNome."]";
            if ( is_array( $stValor ) ) {
                foreach ($sessao->transf4[$aba] as $indice => $lixo) {
                    if ( strpos( $indice, $nome."[".$stNome."]" ) !== false  ) {
                        $sessao->transf4[$aba][$indice] = "";
                    }
                }
                geraNomeVar( $nomeNovo, $stValor);
            } else {
                $sessao->transf4[$aba][$nomeNovo] = $stValor;
            }
        }
    } else {
        $sessao->transf4[$aba][$nome] = $valor;
    }
}
if ( !isset( $ctrl ) ) {
    $ctrl = 0;
    $sessao->transf4 = array();
} else {
    //SETA OS VALORES DA ABA ANTERIOR NA VAR DE SESSAO
    foreach ($_POST as $stCampo => $stValor) {
        if ($stCampo != "aba" and $stCampo != "ctrl" and $stCampo != "anoCenso" and $stCampo != "anoPrograma") {
            geraNomeVar( $stCampo, $stValor );
        }
    }

    //BUSCA OS VALORES DA VAR DE SESSAO DO FORM CORRENTE
    if ( count( $sessao->transf4[$arAbaAtual[$ctrl]] ) ) {
        foreach ($sessao->transf4[$arAbaAtual[$ctrl]] as $campo => $valor) {
            if ( strtoupper($valor) != "XXX" ) {
                $$campo = $valor;
            } else {
                $$campo = "";
            }
        }
    }
}
?>
<script type="text/javascript">
<!--

function mudaAba(aba)
{
         document.frm.target = "telaPrincipal";
<?php
    if ($sessao->transf4['cidadao']['respDomicilio'] != "t") {
?>
        if (aba == 4) {
            aba = <?=$ctrl;?>;
            alertaAviso("Aba disponível apenas para o responsável pelo domicílio!",'form','erro','<?=$sessao->id;?>');
        }
<?php
}
?>
        document.frm.ctrl.value = aba;
        document.frm.submit();
}

function Voltar()
{
    document.frm.target = "telaPrincipal";
    document.frm.action = "consultaCidadao.php?<?=$sessao->id?>&pagina=<?=$pagina?>";
    document.frm.submit();
}
//-->
</script>
<form action="<?=$PHP_SELF;?>?<?=$sessao->id?>" method="post" name="frm" onSubmit="return false;">
<input type="hidden" name="ctrl" value="">
<table width="100%">
    <tr>
<?php
    $flagQuebra = 0;
    foreach ($arAba as $codAba => $nomAba) {
        if ($codAba == $ctrl) {
            echo "        <td width='24%' class='show_dados_center'><b>".$nomAba."</b></td>\n";
        } else {
            echo "        <td width='24%' class='labelcenter'><a href='JavaScript: mudaAba(".$codAba.")'>".$nomAba."</a></td>\n";
        }
        $flagQuebra++;
        if ($flagQuebra == 4) {
            echo "    </tr>\n";
            echo "    <tr>\n";
        }
    }
?>
</tr>
</table>
<?php
switch ($ctrl) {
    case 0:
?>
<input type="hidden" name="aba" value="cidadao">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Relação cidadão/domicílio
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Descrição do domicílio">Domicílio</td>
        <td class="field" width="80%">
            &nbsp;<?=$codDomicilio?>&nbsp;-&nbsp;<?=$nomDomicilio;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Responsável pelo domicílio">Responsável</td>
        <td class="field" width="80%">
<?php
    if ($respDomicilio == "t") {
        $Checked = "Sim";
    } else {
        $Checked = "Não";
    }

?>
            &nbsp;<?=$Checked;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="CGM do cidadão">CGM</td>
        <td class="field" width="80%">
            &nbsp;<?=$numCgm;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Nome completo do cidadão">Nome completo</td>
        <td class="field" width="80%">
            &nbsp;<?=$nomCgm;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data de nascimento do cidadão">Data de nascimento</td>
        <td class="field" width="80%">
            &nbsp;<?=$dataNasc;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Sexo do cidadão">Sexo</td>
        <td class="field" width="80%">
<?php
    if ( $sexoCidadao == "1" or empty($sexoCidadao) ) {
        $Checked = "Masculino";
    } else {
        $Checked = "Feminino";
    }

?>
            &nbsp;<?=$Checked;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Raça/cor do cidadão">Raça/cor</td>
        <td class="field" width="80%">
            &nbsp;<?=$codRacaCidadao?>&nbsp;-&nbsp;<?=pegaDado("nom_raca", "cse.raca", " WHERE cod_raca = ".$codRacaCidadao );?>
        </td>
    </tr>
        <tr>
        <td class="label" width="20%" title="País de origem do cidadão">Pais de origem</td>
        <td class="field" width="80%">
            &nbsp;<?=$paisOrCidadao;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Telefone celular do cidadão">Celular</td>
        <td class="field" width="80%">
            &nbsp;<?=$telCelular;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Estado de origem do cidadão">Estado de origem</td>
        <td class="field" width="80%">
            &nbsp;<?=$estado;?>&nbsp;-&nbsp;<?=pegaDado("nom_uf", "sw_uf", " WHERE cod_uf = ".$estado );?>
        </td>
    </tr>
    <tr>
        <td class="label" title="Município de origem do cidadão">Município de origem</td>
        <td class="field">
            &nbsp;<?=$municipio;?>&nbsp;-&nbsp;<?=pegaDado("nom_municipio", "sw_municipio", " WHERE cod_municipio = ".$municipio );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Nome completo do pai">Nome do pai</td>
        <td class="field" width="80%">
            &nbsp;<?=$nomPai;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Nome completo da mãe">Nome da mãe</td>
        <td class="field" width="80%">
            <?=$nomMae;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Estado civil do cidadão">Estado civil</td>
        <td class="field" width="80%">
             &nbsp;<?=$codEstadoCivil?>&nbsp;-&nbsp;<?=pegaDado("nom_estado", "cse.estado_civil", " WHERE cod_estado = ".$codEstadoCivil );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="No caso de deficientes, selecione a deficiência">Tipo de deficiência</td>
        <td class="field" width="80%">
            &nbsp;<?=$codDeficiencia?>&nbsp;-&nbsp;<?=pegaDado("nom_deficiencia", "cse.deficiencia", " WHERE cod_deficiencia = ".$codDeficiencia );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Tempo de moradia no domicílio">Tempo de moradia</td>
        <td class="field" width="80%">
            <?php
                $arUnidadeMedida = preg_split( "/-/", $codUnidadeMedida);
                if ( empty($arUnidadeMedida[0]) ) {
                    $arUnidadeMedida[0] = 0;
                    $arUnidadeMedida[1] = 0;
                }
            ?>
            &nbsp;<?=$tempoMoradia;?>&nbsp;-&nbsp;<?=pegaDado("nom_unidade", "administracao.unidade_medida", " WHERE  cod_unidade = ".$arUnidadeMedida[0]." and cod_grandeza = ".$arUnidadeMedida[1]." " );?>
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <input type="button" name="voltar" value="Voltar" onClick="javascript: Voltar()" >
        </td>
    </tr>
</table>
</form>

<?php
    break;
    case 1:
?>
<input type="hidden" name="aba" value="documentacao">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Documentação do cidadão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número de identificação social">Identificação social</td>
        <td class="field" width="80%">
            &nbsp;<?=$numIdentSocial;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número do cartão nacional de saúde">Cartão nacional de saúde</td>
        <td class="field" width="80%">
            &nbsp;<?=$numCartSaude;?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            Certidão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Tipo de certidão">Tipo</td>
        <td class="field" width="80%">
            &nbsp;<?=$codCertidao?>&nbsp;-&nbsp;<?=pegaDado("nom_certidao", "cse.tipo_certidao", " WHERE  cod_certidao = ".$codCertidao." " );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número do termo de certidão">Termo</td>
        <td class="field" width="80%">
            &nbsp;<?=$numTermo;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número do livro de certidão">Livro</td>
        <td class="field" width="80%">
            &nbsp;<?=$numLivro;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número da folha de certidão">Folha</td>
        <td class="field" width="80%">
            &nbsp;<?=$numFolha;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data da emissão da certidão">Emissão</td>
        <td class="field" width="80%">
            &nbsp;<?=$dataEmissao;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data de entrada no Brasil no caso de ">Entrada no Brasil</td>
        <td class="field" width="80%">
            &nbsp;<?=$dataEntradaBrasil;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Estado da certidão">UF</td>
        <td class="field" width="80%">
            &nbsp;<?=$uf;?>&nbsp;-&nbsp;<?=pegaDado("nom_uf", "sw_uf", " WHERE  cod_uf = ".$uf." " );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Nome do cartório da certidão">Nome do cartório</td>
        <td class="field" width="80%">
            &nbsp;<?=$nomCartorio;?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            Documento de identidade
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Identidade do cidadão">RG</td>
        <td class="field" width="80%">
            &nbsp;<?=$rg;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Complemento da identidade do cidadão">Complemento</td>
        <td class="field" width="80%">
            &nbsp;<?=$complentoRg;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Órgão emissor do RG">Órgão/emissor</td>
        <td class="field" width="80%">
            &nbsp;<?=$orgaoEmissor;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Estado do RG">UF</td>
        <td class="field" width="80%">
            &nbsp;<?=$ufRg;?>&nbsp;-&nbsp;<?=pegaDado("nom_uf", "sw_uf", " WHERE  cod_uf = ".$ufRg." " );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data de emissão do RG">Emissão</td>
        <td class="field" width="80%">
            &nbsp;<?=$dataEmissaoRg;?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            CTPS
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número da CTPS">Número</td>
        <td class="field" width="80%">
            &nbsp;<?=$numCtps;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Série da CTPS">Série</td>
        <td class="field" width="80%">
            &nbsp;<?=$serieCtps;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Estado da CTPS">UF</td>
        <td class="field" width="80%">
            &nbsp;<?=$ufCtps;?>&nbsp;-&nbsp;<?=pegaDado("nom_uf", "sw_uf", " WHERE  cod_uf = ".$ufCtps." " );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data da emissão da CTPS">Emissão</td>
        <td class="field" width="80%">
            &nbsp;<?=$dataEmissaoCtps;?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            Título de eleitor
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número do título de eleitor">Número</td>
        <td class="field" width="80%">
            &nbsp;<?=$numTitEleitor;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Seção do título de eleitor">Seção</td>
        <td class="field" width="80%">
            &nbsp;<?=$secaoEleitor;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Zona do título de eleitor">Zona</td>
        <td class="field" width="80%">
            &nbsp;<?=$zonaEleitor;?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            Outros
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="CPF do cidadão">CPF</td>
        <td class="field" width="80%">
            &nbsp;<?=$cpf;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="PIS/PASEP do cidadão">PIS/PASEP</td>
        <td class="field" width="80%">
            &nbsp;<?=$pis;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="CBO do cidadão">CBO-R</td>
        <td class="field" width="80%">
            &nbsp;<?=$cbor;?>
        </td>
    </tr>
</table>
</form>

<?php
    break;
    case 2:
?>
<input type="hidden" name="aba" value="escolaridade">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Escolaridade do cidadão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Instituição educacional do cidadão">Instituição educacional</td>
        <td class="field" width="80%">
            &nbsp;<?=$instEducacional;?>&nbsp;-&nbsp;<?=$nomEducacional;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Grau de instrução do cidadão">Grau de instrução</td>
        <td class="field" width="80%">
            &nbsp;<?=$grauInstrucao;?>&nbsp;-&nbsp;<?=$nomInstrucao;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Série/frequência na instituição">Série/frequência</td>
        <td class="field" width="80%">
            <?php
                if ($serie and $frequancia) {
                    echo "&nbsp;".$serie."&nbsp;/&nbsp;".$frequancia;
                } else {
                    echo "&nbsp;";
                }
            ?>
        </td>
    </tr>
</table>
</form>
<?php
    break;
    case 3:
?>
<input type="hidden" name="aba" value="profissionais">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Informações profissionais do cidadão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Empresa em que trabalha">Empresa</td>
        <td class="field" width="80%">
            &nbsp;<?=$codEmpresa;?>&nbsp;-&nbsp;<?=$nomEmpresa;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="CNPJ da empresa">CNPJ</td>
        <td class="field" width="80%">
            &nbsp;<?=$cnpj;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Profissão do cidadão">Profissão</td>
        <td class="field" width="80%">
            &nbsp;<?=$codProfissao;?>&nbsp;-&nbsp;<?=$nomProfissao;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%">Empregado atualmente</td>
        <td class="field" width="80%">
<?php
    if ( $empregado == "T" or empty($empregado) ) {
        $Checked = "Sim";
    } else {
        $Checked = "Não";
    }

?>
            &nbsp;<?=$Checked;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data de admissão na empresa">Admissão</td>
        <td class="field" width="80%">
            &nbsp;<?=$dataAdmissao;?>
        </td>
    </tr>
    <tr>
        <td class="label" width=????" title="Valor do salário">Salário</td>
        <td class="field" width="80%">
            &nbsp;<?=$vlrSalario;?>
        </td>
    </tr>
    <tr>
        <td class="label" width=????" title="Ocupação">Ocupação</td>
        <td class="field" width="80%">
            &nbsp;<?=$ocupacao;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Valor da aposentadoria">Aposentadoria</td>
        <td class="field" width="80%">
            &nbsp;<?=$vlrAposentadoria;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Valor do seguro-desemprego">Seguro-desemprego</td>
        <td class="field" width="80%">
            &nbsp;<?=$vlrSegdesmprego;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Valor da pensão-alimentícia">Pensão-alimentícia</td>
        <td class="field" width="80%">
            &nbsp;<?=$vlrPensaoAlimenticia;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Valor de outras rendas">Outras rendas</td>
        <td class="field" width="80%">
            &nbsp;<?=$vlrOutrasRendas;?>
        </td>
    </tr>
</table>
</form>
<?php
    break;
    case 4:
    if ($sessao->transf4['cidadao'][respDomicilio] == "t") {
?>
<input type="hidden" name="aba" value="despesas">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Valores de despesas mensais do cidadão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com alugel">Aluguel</td>
        <td class="field" width="80%">
            <?=$vlrAluguel;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com prestação da casa própia">Prestação habitacional</td>
        <td class="field" width="80%">
            <?=$vlrDespCasaPropria;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com alimentação">Alimentação</td>
        <td class="field" width="80%">
            <?=$vlrDespAlimentacao;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com água">Água</td>
        <td class="field" width="80%">
            <?=$vlrDespAgua;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com energia elétrica">Energia elétrica</td>
        <td class="field" width="80%">
            <?=$vlrDespEnergia;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com transporte">Transporte</td>
        <td class="field" width="80%">
            <?=$vlrDespTransporte;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com remédios">Remédios</td>
        <td class="field" width="80%">
            <?=$vlrDespRemedio;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com gás">Gás</td>
        <td class="field" width="80%">
            <?=$vlrDespGas;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa diversas">Despesas diversas</td>
        <td class="field" width="80%">
            <?=$vlrDespDiversas;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Quantidade de dependentes da renda">Dependentes da renda</td>
        <td class="field" width="80%">
            <?=$qtdDependentes;?>
        </td>
    </tr>
</table>
</form>
<?php
    }
    break;
    case 5:
?>
<input type="hidden" name="aba" value="vinculo">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Informações de relação familiar
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Grau de parentesco em relação ao responsável">Grau de parentesco</td>
        <td class="field" width="80%">
          &nbsp;<?=$codGrauParentesco?>&nbsp;-&nbsp;<?=pegaDado("nom_grau", "cse.grau_parentesco", " WHERE  cod_grau = ".$codGrauParentesco." " );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Responsável pela criança, na ausência do responsável">Responsável pela criança</td>
        <td class="field" width="80%">
            &nbsp;<?=$respCrianca;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Mês de gestação, no caso de gravidez">Mês de gestação</td>
        <td class="field" width="80%">
            &nbsp;<?=$mesGestacao;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%">Amamentando</td>
        <td class="field" width="80%">
<?php
    if ( $amamentando == "true" or empty($amamentando) ) {
        $Checked = "Sim";
    } else {
        $Checked = "Não";
    }

?>
            &nbsp;<?=$Checked;?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Quantidade de filhos da gestante">Qtd filhos</td>
        <td class="field" width="80%">
            &nbsp;<?=$qtdFilhos;?>
        </td>
    </tr>
</table>
</form>
<?php
    break;
    case 6:
?>
<input type="hidden" name="aba" value="censo">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Ano do censo
        </td>
    </tr>
<?php
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $sQuebra = "<br>";
        $select  = " SELECT ".                  $sQuebra;
        $select .= "    MAX(TO_NUMBER(exercicio,9999)) AS max_exercicio, ".         $sQuebra;
        $select .= "    MIN(TO_NUMBER(exercicio,9999)) AS min_exercicio ".         $sQuebra;
        $select .= " FROM ".                    $sQuebra;
        $select .= "    cse.questao_censo ".    $sQuebra;
        $select = str_replace("<br>", "", $select );
        $dbConfig->abreSelecao($select);
        $dbConfig->fechaBd();
        $anoMax = $dbConfig->pegaCampo("max_exercicio");
        $anoMin = $dbConfig->pegaCampo("min_exercicio");
?>
    <td class="label" width="20%" title="Ano do Censo">Ano do Censo</td>
        <td class="field" width="80%">
            <select name="anoCenso" style="width:200px" onChange="javascript: mudaAba(6)">
                <option value="xxx">Selecione o ano</option>
<?php
        while ($anoMax >= $anoMin) {
            if ($anoMax == $anoCenso) {
                $sSelected = " selected";
            } else {
                $sSelected = "";
            }
            echo "                <option value=\"".$anoMax."\"".$sSelected.">".$anoMax."</option>\n";
            $anoMax--;
        }
?>
            </select>
        </td>
</table>
<?php
    if ( isset($anoCenso) and $anoCenso != "" ) {
?>
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Questões de censo
        </td>
    </tr>
</table>
<?php
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $sQuebra = "<br>";
        $select  = " SELECT ".                  $sQuebra;
        $select .= "    cod_questao, ".         $sQuebra;
        $select .= "    nom_questao, ".         $sQuebra;
        $select .= "    valor_padrao, ".        $sQuebra;
        $select .= "    tipo ".                 $sQuebra;
        $select .= " FROM ".                    $sQuebra;
        $select .= "    cse.questao_censo ".$sQuebra;
        $select .= " WHERE ".                   $sQuebra;
        $select .= "    exercicio = '".$anoCenso."' ".   $sQuebra;
        //echo $select."<br>";
        $select = str_replace("<br>", "", $select );
        if (!(isset($pagina))) {
            $sessao->transf['select'] = $select;
        }

        $paginacao = new paginacaoLegada;
        $paginacao->complemento ="&ctrl=".$ctrl."&anoCenso=".$anoCenso;
        $paginacao->pegaDados($select,"3");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder(" tipo desc, lower(cod_questao)","ASC");
        $sSQL = $paginacao->geraSQL();
        $dbConfig->abreSelecao($sSQL);
        $dbConfig->fechaBd();
        if (!$dbConfig->eof()) {
        echo "    <table width=\"100%\">\n";
        while (!$dbConfig->eof()) {
            switch ( $dbConfig->pegaCampo("tipo") ) {
                case "l":
                $questCenso = "questaoCenso[".$anoCenso."_".$dbConfig->pegaCampo("cod_questao")."]";
                if ($$questCenso) {
                    $valor = $$questCenso;
                }
?>
        <tr>
            <td class="label" width="20%">
                <?=$dbConfig->pegaCampo("nom_questao")?>
            </td>
            <td class="field" width="80%">
                &nbsp;
<?php
        $options = explode("\n", $dbConfig->pegaCampo("valor_padrao") );
        foreach ($options as $option) {
            $option = trim($option);
            //if ( $questaoCenso[$dbConfig->pegaCampo("cod_questao")] ==  $option ) {
            if ($valor ==  $option) {
                echo $valor;
            }
        }
?>
            </td>
        </tr>
<?php
                break;
                case "m":
//$questaoCenso[$dbConfig->pegaCampo("cod_questao")][$chkValue]
?>
        <tr>
            <td class="label" width="20%">
                <?=$dbConfig->pegaCampo("nom_questao")?>
            </td>
            <td class="field" width="80%">
<?php
        $chkValues = explode("\n", $dbConfig->pegaCampo("valor_padrao") );
        $stQuestChk= "";
        foreach ($chkValues as $chkValue) {
            $chkValue = trim($chkValue);
            //if ($valor ==  $chkValue) {
            $questCenso = "questaoCenso[".$anoCenso."_".$dbConfig->pegaCampo("cod_questao")."][".$chkValue."]";
            if ($$questCenso) {
                $stQuestChk .=  "&nbsp;".$chkValue."<br>";
            }
        }
            echo $stQuestChk = substr( $stQuestChk, 0, strlen($stQuestChk) - 4 )."&nbsp;";

?>
            </td>
        </tr>
<?php
                break;
                case "n":
//$questaoCenso[$dbConfig->pegaCampo("cod_questao")]
$questCenso = "questaoCenso[".$anoCenso."_".$dbConfig->pegaCampo("cod_questao")."]";
if ($$questCenso) {
    $valor = $$questCenso;
} else {
    $valor = $dbConfig->pegaCampo("valor_padrao");
}
?>
        </tr>
            <td class="label" width="20%">
                <?=$dbConfig->pegaCampo("nom_questao")?>
            </td>
            <td class="field" width="80%">
                <?=$valor;?>
            </td>
        </tr>
<?php
                break;
                case "t":
?>
        </tr>
            <td class="label" width="20%">
                <?=$dbConfig->pegaCampo("nom_questao")?>
            </td>
            <td class="field" width="80%">
<?php
$questCenso = "questaoCenso[".$anoCenso."_".$dbConfig->pegaCampo("cod_questao")."]";
if ($$questCenso) {
    $valor = $$questCenso;
} else {
    $valor = $dbConfig->pegaCampo("valor_padrao");
}
?>
                <?=$valor;?>
            </td>
        </tr>
<?php
                break;
            }
        $dbConfig->vaiProximo();
        }
    echo "    </table>\n";
    }
?>
<script type="text/javascript">
<!--
function paginacao(linkPagina)
{
    document.frm.pagina.value = linkPagina;
    document.frm.target = "telaPrincipal";
    document.frm.ctrl.value = "6";
    document.frm.submit();
}
//-->
</script>
<table width="450" align="center">
    <tr>
        <td align="center">
            <font size=2>
                <input type="hidden" name="pagina" value="">
<?php
$stPaginacao = strip_tags($paginacao->aux);
$arPaginacao = preg_split( "/\|/", $stPaginacao);
foreach ($arPaginacao as $novoLink) {
    if ( trim($novoLink) == "Anterior" ) {
        $sLinkPagina .= "<a href=\"javascript: paginacao(".(string) --$paginacao->pagina.");\">Anterior</a>\n";
    } elseif (trim($novoLink) == "Próxima" ) {
        $sLinkPagina .= " | <a href=\"javascript: paginacao(".(string) $inProximaPagina.");\">Próxima</a>\n";
    } elseif ( trim($novoLink) != "" ) {
        $novoLink = trim($novoLink) - 1 ;
        if ($pagina  == $novoLink) {
            $sLinkPagina .=  " | <a href=\"javascript: paginacao(".(string) ($novoLink).");\" style=\"color: red\">".++$novoLink."</a>\n";
            $inProximaPagina = $novoLink;
        } else {
            $sLinkPagina .=  " | <a href=\"javascript: paginacao(".(string) ($novoLink).");\">".++$novoLink."</a>\n";
        }
    }
}
echo $sLinkPagina;
?>
            </font>
        </td>
    </tr>
</table>
</form>
<?php
    }
    $dbConfig->limpaSelecao();
    break;
    case 7:
        if ( empty( $pagina ) ) {
            $pagina = 0;
        }
?>
<input type="hidden" name="aba" value="programas">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Ano do programa
        </td>
    </tr>
<?php
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $sQuebra = "<br>";
        $select  = " SELECT ".                  $sQuebra;
        $select .= "    MAX(TO_NUMBER(exercicio,9999)) AS max_exercicio, ".         $sQuebra;
        $select .= "    MIN(TO_NUMBER(exercicio,9999)) AS min_exercicio ".         $sQuebra;
        $select .= " FROM ".                    $sQuebra;
        $select .= "    cse.programa_social ".    $sQuebra;
        $select = str_replace("<br>", "", $select );
        $dbConfig->abreSelecao($select);
        $dbConfig->fechaBd();
        $anoMax = $dbConfig->pegaCampo("max_exercicio");
        $anoMin = $dbConfig->pegaCampo("min_exercicio");
?>
    <td class="label" width="20%" title="Ano do Programa">Ano do Programa</td>
        <td class="field" width="80%">
            <select name="anoPrograma" style="width:200px" onChange="javascript: mudaAba(7)">
                <option value="xxx">Selecione o ano</option>
<?php
        while ($anoMax >= $anoMin) {
            if ($anoMax == $anoPrograma) {
                $sSelected = " selected";
            } else {
                $sSelected = "";
            }
            echo "                <option value=\"".$anoMax."\"".$sSelected.">".$anoMax."</option>\n";
            $anoMax--;
        }
?>
            </select>
        </td>
</table>
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Participação em programas sociais
        </td>
    </tr>
<?php
    if ( isset($anoPrograma) and $anoPrograma != "xxx" ) {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $sQuebra = "<br>";
        $select  = " SELECT ".                  $sQuebra;
        $select .= "    cod_programa, ".        $sQuebra;
        $select .= "    exercicio, ".           $sQuebra;
        $select .= "    nom_programa, ".        $sQuebra;
        $select .= "    descricao ".            $sQuebra;
        $select .= " FROM ".                    $sQuebra;
        $select .= "    cse.programa_social ".    $sQuebra;
        $select .= " WHERE ".                   $sQuebra;
        $select .= "    exercicio = '".$anoPrograma."' ".   $sQuebra;
        //echo $select."<br>";
        $select = str_replace("<br>", "", $select );
        if (!(isset($pagina))) {
            $sessao->transf['select'] = $select;
        }

        $paginacao = new paginacaoLegada;
        //$paginacao->complemento ="&ctrl=".$ctrl."&anoCenso=".$anoCenso;
        $paginacao->pegaDados($select,"3");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder(" exercicio, lower(cod_programa)","ASC");
        $sSQL = $paginacao->geraSQL();
        $dbConfig->abreSelecao($sSQL);
        $dbConfig->fechaBd();
        if (!$dbConfig->eof()) {
            while (!$dbConfig->eof()) {
?>
    <tr>
        <td class="alt_dados" colspan="2">
<?php
$participProgSociais = "ps[".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_cod]";
if ($$participProgSociais) {
    $valor = $$participProgSociais;
} else {
    $valor = "";
}

if ($valor == "true") {
    $checkedProg = true;
} else {
    $checkedProg = false;
}
?>
            &nbsp;<?=$dbConfig->pegaCampo("nom_programa");?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%">
            Data de inclusão
        </td>
        <td class="field" width="80%">
<?php
$dataIncProg = "ps[".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_di]";
if ($$dataIncProg) {
    $valor = $$dataIncProg;
} else {
    $valor = "";
}
?>
            &nbsp;<?=$valor;?>
        </td>
    </tr>
    <tr>
        <td class="label">
            Benefício
        </td>
        <td class="field">
<?php
$vlrBenef = "ps[".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_vl]";
if ($$vlrBenef) {
    $valor = $$vlrBenef;
} else {
    $valor = "";
}
?>
            &nbsp;<?=$valor;?>
        </td>
    </tr>
    <tr>
        <td class="label">
            Beneficiário prioritário
        </td>
        <td class="field">
<?php
$benPrior = "ps[".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_bp]";
if ($$benPrior) {
    $valor = $$benPrior;
} else {
    $valor = "";
}

if ($valor == "true") {
    $checked = "Sim";
} elseif ($checkedProg) {
    $checked = "Não";
} else {
    $checked = "";
}
?>
            &nbsp;<?=$checked;?>
        </td>
    </tr>
<?php
                $dbConfig->vaiProximo();
            }
        }
    }
?>
</table>
<script type="text/javascript">
<!--
function paginacao(linkPagina)
{
    document.frm.pagina.value = linkPagina;
    document.frm.target = "telaPrincipal";
    document.frm.ctrl.value = "7";
    document.frm.submit();
}
//-->
</script>
<table width="450" align="center">
    <tr>
        <td align="center">
            <font size=2>
                <input type="hidden" name="pagina" value="">
<?php
$stPaginacao = strip_tags($paginacao->aux);
$arPaginacao = preg_split( "/\|/", $stPaginacao);
foreach ($arPaginacao as $novoLink) {
    if ( trim($novoLink) == "Anterior" ) {
        $sLinkPagina .= "<a href=\"javascript: paginacao(".(string) --$paginacao->pagina.");\">Anterior</a>\n";
    } elseif (trim($novoLink) == "Próxima" ) {
        $sLinkPagina .= " | <a href=\"javascript: paginacao(".(string) $inProximaPagina.");\">Próxima</a>\n";
    } elseif ( trim($novoLink) != "" ) {
        $novoLink = trim($novoLink) - 1 ;
        if ($pagina  == $novoLink) {
            $sLinkPagina .=  " | <a href=\"javascript: paginacao(".(string) ($novoLink).");\" style=\"color: red\">".++$novoLink."</a>\n";
            $inProximaPagina = $novoLink;
        } else {
            $sLinkPagina .=  " | <a href=\"javascript: paginacao(".(string) ($novoLink).");\">".++$novoLink."</a>\n";
        }
    }
}
echo $sLinkPagina;
?>
            </font>
        </td>
    </tr>
</table>
</form>
<?php
    break;
}
?>
